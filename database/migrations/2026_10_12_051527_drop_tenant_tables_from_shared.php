<?php

use App\Services\TenantDatabase;
use App\Services\TenantTables;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bersihkan tabel tenant yang tertinggal di database shared (MySQL).
 *
 * Sejak data akuntansi pindah ke file SQLite per-user, tabel seperti
 * `akun_perkiraan`, `pengaturan`, `penjualans`, dst. tidak lagi dipakai di
 * database shared. Migrasi ini menghapusnya sekali agar tidak ada lagi tabel
 * yang menumpuk, dan karena sudah tercatat di tabel `migrations`, `migrate`
 * berikutnya tidak akan membuatnya lagi.
 *
 * Sifat:
 *  - forward-only (down() sengaja kosong — tidak ada yang perlu dibangun ulang)
 *  - hanya menyentuh tabel tenant dari TenantTables, tabel shared (users,
 *    sessions, cache, jobs, donasis, chat, user_databases) tidak disentuh
 *  - di-skip saat migrasi tenant dan saat driver shared bukan mysql (mis. test
 *    sqlite in-memory) supaya suite tetap utuh
 */
return new class extends Migration
{
    /**
     * Tabel lama yang tidak lagi punya model/pemakaian runtime di shared.
     *
     * `banks` dan `kas` sudah digantikan oleh `rekenings` (lihat
     * create_rekenings_merge) tetapi tabelnya masih tertinggal.
     *
     * @var array<int, string>
     */
    private const LEGACY_TABLES = ['banks', 'kas'];

    public function up(): void
    {
        $shared = TenantDatabase::SHARED_CONNECTION;

        // Pada migrasi anonim getConnection() selalu null, jadi deteksi konteks
        // lewat default connection: proses `migrate --database=tenant` mengarahkan
        // default ke 'tenant' (lihat TenantDatabase::provisionLocked), sedangkan
        // migrasi shared berjalan dengan default 'mysql'.
        if (DB::getDefaultConnection() !== $shared) {
            return;
        }

        if (DB::connection($shared)->getDriverName() !== 'mysql') {
            return;
        }

        $tables = array_values(array_unique(array_merge(
            TenantTables::existingOnShared(),
            array_filter(
                self::LEGACY_TABLES,
                fn (string $table): bool => Schema::connection($shared)->hasTable($table),
            ),
        )));

        if ($tables === []) {
            return;
        }

        Schema::connection($shared)->disableForeignKeyConstraints();

        try {
            foreach ($tables as $table) {
                Schema::connection($shared)->dropIfExists($table);
            }
        } finally {
            Schema::connection($shared)->enableForeignKeyConstraints();
        }
    }

    public function down(): void
    {
        //
    }
};
