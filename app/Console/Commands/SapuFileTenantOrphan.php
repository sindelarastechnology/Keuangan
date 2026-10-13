<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserDatabase;
use App\Services\TenantDatabase;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Menemukan dan menghapus file database tenant yang tidak lagi punya pemilik.
 *
 * Hook `User::deleting` sudah menghapus file whenever user dihapus lewat
 * Eloquent, tapi `TenantDatabase::drop()` sengaja menjadi no-op ketika fitur
 * per-user nonaktif. Akibatnya user yang dihapus saat fitur dimatikan (atau
 * dihapus lewat jalur non-Eloquent) meninggalkan file SQLite di disk. File
 * semacam itu berbahaya: bila user_id yang sama kemudian dipakai akun baru,
 * `provision()` akan membangun ulang schemanya di atas data milik akun lama.
 *
 * Perintah ini menutup celah itu: folder di storage tenant tanpa baris users
 * maupun user_databases dianggap yatim dan dihapus. Sama seperti perintah
 * pembersih lain, penghapusan hanya berjalan dengan --force.
 */
#[Signature('akun:sapu-file-tenant
    {--dry-run : Tampilkan daftar saja tanpa menghapus}
    {--force : Benar-benar menghapus file yatim (wajib tanpa --dry-run)}')]
#[Description('Hapus file database tenant yang sudah tidak punya pemilik user.')]
class SapuFileTenantOrphan extends Command
{
    public function handle(): int
    {
        if (! TenantDatabase::enabled()) {
            $this->error('Fitur database per-user nonaktif (TENANT_DB_ENABLED=false).');

            return self::FAILURE;
        }

        $folder = rtrim((string) config('tenant.path'), '/\\');

        if (! File::isDirectory($folder)) {
            $this->info("Folder tenant belum ada: {$folder}");

            return self::SUCCESS;
        }

        $eksekusi = (bool) $this->option('force') && ! $this->option('dry-run');

        if (! $eksekusi) {
            $this->info('Mode laporan (gunakan --force untuk menghapus, --dry-run untuk simulasi).');
        }

        $this->newLine();

        $yatim = $this->fileYatim($folder);

        if ($yatim === []) {
            $this->info('Tidak ada file tenant yatim. Semua file punya pemilik user yang sah.');

            return self::SUCCESS;
        }

        foreach ($yatim as $item) {
            $alasan = $item['user'] === null
                ? 'user sudah tidak ada'
                : 'mapping user_databases hilang';

            $this->line("  user_id={$item['user_id']}: {$item['file']} ({$alasan})");

            if ($eksekusi) {
                $this->hapusFolder($item['path']);
            }
        }

        $this->newLine();
        $this->info(count($yatim).' file tenant yatim '.($eksekusi ? 'dihapus.' : 'ditemukan.'));

        if (! $eksekusi) {
            $this->line('Jalankan ulang dengan --force untuk menghapusnya.');
        }

        return self::SUCCESS;
    }

    /**
     * @return array<int, array{user_id:int, path:string, file:string, user:int|null}>
     */
    private function fileYatim(string $folder): array
    {
        $adaUserDatabase = UserDatabase::query()
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $adaUser = User::query()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $hasil = [];

        foreach (File::directories($folder) as $directory) {
            $namaFolder = basename($directory);

            if (! ctype_digit($namaFolder)) {
                continue;
            }

            $userId = (int) $namaFolder;

            if (! File::exists(TenantDatabase::pathFor($userId))) {
                continue;
            }

            if (in_array($userId, $adaUserDatabase, true) && in_array($userId, $adaUser, true)) {
                continue;
            }

            $hasil[] = [
                'user_id' => $userId,
                'path' => $directory,
                'file' => basename(TenantDatabase::pathFor($userId)),
                'user' => in_array($userId, $adaUser, true) ? $userId : null,
            ];
        }

        return $hasil;
    }

    /**
     * Hapus satu folder tenant beserta seluruh isinya.
     */
    private function hapusFolder(string $directory): void
    {
        File::deleteDirectory($directory);
    }
}
