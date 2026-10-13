<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\TenantDatabase;
use App\Services\TenantTables;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Memindahkan data tenant lama (yang masih menumpuk di satu database shared)
 * ke file SQLite milik masing-masing user.
 *
 * Sumber dibaca dari koneksi shared (MySQL) secara read-only. Target ditulis ke
 * file tenant user. Perintah ini aman diulang: user yang sudah punya data akan
 * dilewati kecuali dijalankan dengan --force (baris lama di file tenant user
 * tersebut dihapus lebih dulu agar tidak bentrok).
 */
#[Signature('akun:backfill-tenant-db
    {--user=* : Batasi hanya user tertentu (ID, boleh berulang)}
    {--dry-run : Tampilkan rencana tanpa menulis file apa pun}
    {--force : Tulis ulang user yang databasenya sudah terisi}')]
#[Description('Pindahkan data akuntansi lama dari database shared ke file database per-user.')]
class BackfillTenantDatabase extends Command
{
    private const CHUNK = 500;

    public function handle(): int
    {
        if (! TenantDatabase::enabled()) {
            $this->error('Fitur database per-user nonaktif (TENANT_DB_ENABLED=false). Jalankan dengan TENANT_DB_ENABLED=true.');

            return self::FAILURE;
        }

        $shared = TenantDatabase::SHARED_CONNECTION;
        $tenant = TenantDatabase::connectionName();

        $tables = TenantTables::existingOnShared();

        if ($tables === []) {
            $this->error('Tidak ada tabel tenant di database shared. Tidak ada yang perlu dipindahkan.');

            return self::SUCCESS;
        }

        $this->info('Tabel tenant di database shared: '.count($tables));
        $this->line('  '.$this->shorten(implode(', ', $tables)));

        $users = $this->penggunaDiproses();

        if ($users->isEmpty()) {
            $this->warn('Tidak ada user yang perlu diproses.');

            return self::SUCCESS;
        }

        $this->info('Memproses '.$users->count().' user...');
        $this->newLine();

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            $this->backfillUser($user, $tables, $shared, $tenant);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        TenantDatabase::disconnect();

        $this->reportOrphans($tables, $shared);

        $this->info('Backfill selesai.');

        return self::SUCCESS;
    }

    /**
     * @return Collection<int, User>
     */
    private function penggunaDiproses()
    {
        $query = User::query()->orderBy('id');

        $ids = array_filter(array_map('intval', (array) $this->option('user')));

        if ($ids !== []) {
            $query->whereIn('id', $ids);
        }

        return $query->get();
    }

    /**
     * @param  array<int, string>  $tables
     */
    private function backfillUser(User $user, array $tables, string $shared, string $tenant): void
    {
        $sumber = DB::connection($shared);
        $jumlahBaris = 0;

        if ($this->option('dry-run')) {
            foreach ($tables as $table) {
                $jumlahBaris += (int) $sumber->table($table)->where('user_id', $user->id)->count();
            }

            $this->line("  [dry-run] #{$user->id} {$user->email}: {$jumlahBaris} baris");

            return;
        }

        $sudahTerisi = TenantDatabase::hasDatabase($user);

        if ($sudahTerisi && ! $this->option('force')) {
            $this->line("  [dilewati] #{$user->id} {$user->email}: tenant sudah terisi (pakai --force untuk menimpa)");

            return;
        }

        if ($sudahTerisi && $this->option('force')) {
            $this->backupFileTenant($user);
        }

        TenantDatabase::provision($user);
        TenantDatabase::connect($user);

        try {
            // provision() menulis marker kepemilikan lebih dulu (satu baris di
            // pengaturan). Marker itu bisa memakai id yang sama dengan baris lama
            // sehingga salinan bentrok PK. Hapus dulu supaya id asli dipertahankan,
            // lalu tulis ulang setelah seluruh tabel selesai disalin.
            $this->hapusMarkerKepemilikan($user->id);

            foreach ($tables as $table) {
                $jumlahBaris += $this->salinTabel($table, $user->id, $sumber, $tenant);
            }

            $this->tulisMarkerKepemilikan($user->id);

            $this->line("  #{$user->id} {$user->email}: {$jumlahBaris} baris → ".TenantDatabase::pathFor($user));
        } finally {
            TenantDatabase::disconnect();
        }
    }

    /**
     * Salin cadangan file tenant sebelum ditimpa ulang saat --force.
     */
    private function backupFileTenant(User $user): void
    {
        $path = TenantDatabase::pathFor($user);

        if (! File::exists($path)) {
            return;
        }

        $tujuan = $path.'.'.now()->format('YmdHis').'.bak';
        File::copy($path, $tujuan);

        $this->line("  backup file tenant #{$user->id} → {$tujuan}");
    }

    /**
     * Hapus marker kepemilikan agar id baris lama bisa disalin apa adanya.
     */
    private function hapusMarkerKepemilikan(int $userId): void
    {
        DB::connection(TenantDatabase::connectionName())
            ->table('pengaturan')
            ->where('user_id', $userId)
            ->where('key', TenantDatabase::OWNER_MARKER_KEY)
            ->delete();
    }

    /**
     * Tulis ulang marker kepemilikan setelah backfill selesai.
     *
     * Wajib dipanggil di akhir: tanpa marker, skemaLengkap() menganggap file
     * tidak sah dan provisioning berikutnya menghapus & membangun ulang file.
     */
    private function tulisMarkerKepemilikan(int $userId): void
    {
        DB::connection(TenantDatabase::connectionName())
            ->table('pengaturan')
            ->updateOrInsert(
                ['user_id' => $userId, 'key' => TenantDatabase::OWNER_MARKER_KEY],
                ['value' => (string) $userId, 'updated_at' => now(), 'created_at' => now()],
            );
    }

    /**
     * Salin seluruh baris satu tabel milik user ke file tenantnya.
     */
    private function salinTabel(string $table, int $userId, $sumber, $tujuan): int
    {
        if (! $this->option('dry-run') && $this->option('force')) {
            DB::connection($tujuan)->table($table)->where('user_id', $userId)->delete();
        }

        $tersalin = 0;

        $sumber->table($table)
            ->where('user_id', $userId)
            ->orderBy('id')
            ->chunk(self::CHUNK, function ($rows) use ($table, $tujuan, &$tersalin): void {
                $data = $rows->map(fn ($row) => (array) $row)->all();

                DB::connection($tujuan)->table($table)->insert($data);

                $tersalin += count($data);
            });

        return $tersalin;
    }

    /**
     * Laporkan baris yatim: user_id NULL atau user yang sudah tidak ada.
     *
     * Data yatim tidak dihapus otomatis — operators tetap memutuskan sendiri
     * agar tidak ada data hilang tanpa sengaja.
     *
     * @param  array<int, string>  $tables
     */
    private function reportOrphans(array $tables, string $shared): void
    {
        $sumber = DB::connection($shared);
        $adaUser = User::query()->pluck('id')->all();
        $perOrphan = [];

        foreach ($tables as $table) {
            foreach ($sumber->table($table)->select('user_id')->distinct()->pluck('user_id') as $userId) {
                if ($userId === null || ! in_array((int) $userId, $adaUser, true)) {
                    $kunci = $userId === null ? 'NULL' : $userId;
                    $perOrphan[$kunci] = ($perOrphan[$kunci] ?? 0) + 1;
                }
            }
        }

        if ($perOrphan === []) {
            $this->info('Tidak ada baris yatim (semua user_id dimiliki user yang valid).');

            return;
        }

        $this->warn('Baris yatim terdeteksi (user_id NULL / user tidak ada) dan TIDAK dipindahkan:');

        foreach ($perOrphan as $userId => $jumlah) {
            $this->line("  user_id={$userId}: {$jumlah} baris");
        }
    }

    private function shorten(string $value): string
    {
        return Str::limit($value, 110);
    }
}
