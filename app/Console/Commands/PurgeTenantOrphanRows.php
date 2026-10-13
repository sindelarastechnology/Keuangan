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

/**
 * Membersihkan baris tenant yang `user_id`-nya NULL.
 *
 * Baris ini berasal dari migrasi lama yang menyisipkan data default (gudang
 * GDG-0001, rekanan UMUM, akun 526) tanpa mengisi user_id. Baris tersebut tidak
 * terlihat di UI karena global scope menyaringnya, tapi menggandakan data milik
 * user dan Bentrok dengan UNIQUE(user_id, kode).
 *
 * Perintah ini hanya menghapus baris tanpa pemilik; seluruh baris yang sudah
 * punya user_id tidak disentuh.
 */
#[Signature('akun:bersihkan-data-null
    {--user=* : Batasi hanya user tertentu (ID, boleh berulang)}
    {--dry-run : Hitung dan tampilkan, tanpa menghapus apa pun}
    {--force : Benar-benar menghapus (tanpa --force hanya menampilkan laporan)}')]
#[Description('Hapus baris tenant tanpa user_id dari seluruh file database per-user.')]
class PurgeTenantOrphanRows extends Command
{
    public function handle(): int
    {
        if (! TenantDatabase::enabled()) {
            $this->error('Fitur database per-user nonaktif (TENANT_DB_ENABLED=false).');

            return self::FAILURE;
        }

        $tables = TenantTables::all();

        if ($tables === []) {
            $this->error('Tidak ada tabel tenant yang dikenal.');

            return self::FAILURE;
        }

        $eksekusi = (bool) $this->option('force') && ! $this->option('dry-run');

        if (! $eksekusi) {
            $this->info('Mode laporan (gunakan --force untuk menghapus, --dry-run untuk simulasi).');
        }

        $this->newLine();

        $grandTotal = 0;

        foreach ($this->penggunaDiproses() as $user) {
            if (! TenantDatabase::hasDatabase($user)) {
                $this->warn("  #{$user->id} {$user->email}: lewati, database tenant belum siap.");

                continue;
            }

            $detail = $this->bersihkanUser($user, $tables, $eksekusi);
            $jumlah = array_sum($detail);

            if ($jumlah > 0) {
                $this->line("  #{$user->id} {$user->email}:".($eksekusi ? ' dihapus ' : ' akan dihapus ').$jumlah.' baris');

                foreach ($detail as $table => $n) {
                    $this->line("      - {$table}: {$n}");
                }
            }

            $grandTotal += $jumlah;
        }

        $this->newLine();

        if (! $eksekusi) {
            $this->info("Total baris tanpa user_id: {$grandTotal}.");
            $this->line('Jalankan ulang dengan --force untuk menghapusnya.');

            return self::SUCCESS;
        }

        if ($grandTotal === 0) {
            $this->info('Tidak ada baris tanpa user_id. Sudah bersih.');

            return self::SUCCESS;
        }

        $this->info("Selesai: {$grandTotal} baris tanpa user_id dihapus.");

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
     * @return array<string, int> jumlah baris per tabel
     */
    private function bersihkanUser(User $user, array $tables, bool $eksekusi): array
    {
        $detail = [];

        TenantDatabase::connect($user);

        try {
            $schema = DB::connection(TenantDatabase::connectionName())->getSchemaBuilder();

            foreach ($tables as $table) {
                if (! $schema->hasTable($table)) {
                    continue;
                }

                $jumlah = DB::table($table)->whereNull('user_id')->count();

                if ($jumlah === 0) {
                    continue;
                }

                if ($eksekusi) {
                    DB::table($table)->whereNull('user_id')->delete();
                }

                $detail[$table] = $jumlah;
            }
        } finally {
            TenantDatabase::disconnect();
        }

        return $detail;
    }
}
