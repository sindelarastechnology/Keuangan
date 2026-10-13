<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\TenantDatabase;
use App\Services\TenantTables;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menghapus tabel tenant yang sudah tidak lagi dipakai dari database shared.
 *
 * Sengaja BUKAN migration: penghapusan 40+ tabel bersifat destruktif dan hanya
 * boleh dijalankan operator setelah akun:backfill-tenant-db selesai dan data
 * per-user sudah diverifikasi. Wajib --force.
 */
#[Signature('akun:bersihkan-tabel-tenant {--force : Jalankan penghapusan tanpa konfirmasi}')]
#[Description('Hapus tabel tenant dari database shared setelah backfill per-user selesai. Membutuhkan --force.')]
class PurgeSharedTenantTables extends Command
{
    public function handle(): int
    {
        if (! TenantDatabase::enabled()) {
            $this->error('Fitur database per-user nonaktif (TENANT_DB_ENABLED=false).');

            return self::FAILURE;
        }

        $shared = TenantDatabase::SHARED_CONNECTION;
        $tables = TenantTables::existingOnShared();

        if ($tables === []) {
            $this->info('Tidak ada tabel tenant di database shared. Sudah bersih.');

            return self::SUCCESS;
        }

        $this->warn('Perintah ini menghapus permanen tabel berikut dari database shared:');
        $this->newLine();
        $this->line('  '.implode(', ', $tables));
        $this->newLine();

        $punyaData = [];
        $userIds = [];

        foreach ($tables as $table) {
            $jumlah = (int) DB::connection($shared)->table($table)->count();

            if ($jumlah > 0) {
                $punyaData[$table] = $jumlah;
            }

            foreach (DB::connection($shared)->table($table)->distinct()->pluck('user_id') as $userId) {
                if ($userId !== null) {
                    $userIds[(int) $userId] = true;
                }
            }
        }

        if ($punyaData !== []) {
            $this->warn('Tabel berikut MASIH berisi data:');

            foreach ($punyaData as $table => $jumlah) {
                $this->line("  {$table}: {$jumlah} baris");
            }

            $this->newLine();
        }

        $tanpaFile = [];

        foreach (array_keys($userIds) as $userId) {
            $user = User::query()->find($userId);

            if (! $user || ! TenantDatabase::hasDatabase($user)) {
                $tanpaFile[] = $userId;
            }
        }

        if ($tanpaFile !== []) {
            $this->error('PEMBATASAN: data milik user berikut tidak punya database tenant, jadi belum aman dihapus:');

            foreach ($tanpaFile as $userId) {
                $this->line("  user_id={$userId}");
            }

            $this->newLine();
            $this->error('Jalankan akun:backfill-tenant-db lalu verifikasi ulang sebelum membersihkan.');

            return self::FAILURE;
        }

        if (! $this->option('force')) {
            $this->warn('Dibatalkan. Tambahkan --force untuk benar-benar menghapus.');

            return self::FAILURE;
        }

        Schema::connection($shared)->disableForeignKeyConstraints();

        foreach ($tables as $table) {
            Schema::connection($shared)->drop($table);
            $this->line("  drop {$table}");
        }

        Schema::connection($shared)->enableForeignKeyConstraints();

        $this->info(count($tables).' tabel tenant dihapus dari database shared.');

        return self::SUCCESS;
    }
}
