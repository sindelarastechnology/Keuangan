<?php

namespace App\Services;

use App\Traits\BelongsToUser;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

/**
 * Daftar tabel yang menjadi milik user (tenant) dan pindah ke database SQLite
 * per-user.
 *
 * Diturunkan dari model yang memakai trait BelongsToUser DAN tidak mengunci
 * koneksi sendiri. Model shared (User, Donasi, Chat*, UserDatabase,
 * SharedNotification) sengaja dikecualikan karena kolomnya tidak punya user_id
 * atau karena tabelnya tetap tinggal di database shared.
 *
 * Resolver dipakai oleh perintah backfill dan pembersihan supaya tidak ada
 * daftar tabel manual yang bisa basi ketika model baru ditambahkan.
 */
class TenantTables
{
    /**
     * @return array<int, string> nama tabel tenant, urut alfabetis
     */
    public static function all(): array
    {
        $tables = [];

        foreach (glob(app_path('Models/*.php')) ?: [] as $file) {
            $class = 'App\\Models\\'.basename($file, '.php');

            if (! class_exists($class)) {
                continue;
            }

            if (! in_array(BelongsToUser::class, class_uses_recursive($class), true)) {
                continue;
            }

            try {
                $model = new $class;
            } catch (Throwable) {
                continue;
            }

            if ($model->getConnectionName() !== null) {
                continue;
            }

            $tables[] = $model->getTable();
        }

        $tables = array_values(array_unique($tables));
        sort($tables);

        return $tables;
    }

    /**
     * Tabel tenant yang benar-benar ada pada koneksi shared (untuk dibersihkan).
     *
     * @return array<int, string>
     */
    public static function existingOnShared(): array
    {
        $shared = TenantDatabase::SHARED_CONNECTION;

        return array_values(array_filter(self::all(), function (string $table) use ($shared): bool {
            return Schema::connection($shared)->hasTable($table);
        }));
    }

    /**
     * Nama tabel yang enak dibaca untuk laporan.
     *
     * @param  array<int, string>  $tables
     */
    public static function summarize(array $tables): string
    {
        if ($tables === []) {
            return '(tidak ada)';
        }

        return Str::limit(implode(', ', $tables), 120);
    }
}
