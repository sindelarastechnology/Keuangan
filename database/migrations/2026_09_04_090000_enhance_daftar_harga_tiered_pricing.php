<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daftar_harga', function (Blueprint $table) {
            // Tambah kolom baru untuk tier pricing + historis
            $table->decimal('min_qty', 12, 2)->default(1)->after('is_aktif');
            $table->decimal('max_qty', 12, 2)->nullable()->after('min_qty');
            $table->date('tanggal_mulai')->nullable()->after('max_qty');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            $table->string('keterangan')->nullable()->after('tanggal_selesai');
        });

        // Hapus unique constraint lama yang menghalangi multi-tier pricing
        // Cek dulu apakah index ada (SQLite vs MySQL)
        //
        // MySQL: index unik (supplier_id, barang_id) menjadi backing index FK
        // supplier_id, sehingga DROP INDEX ditolak (error 1553) selama FK masih
        // ada — try/catch tidak boleh dipakai untuk menyembunyikan itu karena
        // unique-nya akan SELALU tertinggal dan multi-tier pricing (beberapa
        // baris per barang dengan min_qty berbeda) rusak. Solusi: drop FK dulu,
        // drop index, lalu buat ulang FK.
        $isMysql = DB::getDriverName() === 'mysql';
        $foreignKeys = fn (): array => Schema::getForeignKeys('daftar_harga');
        $hasSupplierFk = fn (): bool => collect($foreignKeys())
            ->contains(fn ($fk) => in_array('supplier_id', (array) ($fk['columns'] ?? []), true));

        if ($isMysql && $hasSupplierFk()) {
            Schema::table('daftar_harga', function (Blueprint $table) {
                $table->dropForeign(['supplier_id']);
            });
        }

        $indexes = DB::select($isMysql
            ? "SHOW INDEX FROM daftar_harga WHERE Key_name LIKE '%unique%'"
            : "SELECT name FROM sqlite_master WHERE type='index' AND tbl_name='daftar_harga' AND name LIKE '%unique%'"
        );

        // SHOW INDEX MySQL mengembalikan SATU baris per kolom index komposit,
        // jadi nama index bisa muncul lebih dari sekali — dedupe sebelum drop.
        $uniqueNames = collect($indexes)
            ->map(fn ($idx) => $idx->name ?? $idx->Key_name ?? null)
            ->filter(fn ($n) => $n && $n !== 'PRIMARY')
            ->unique()
            ->values();

        foreach ($uniqueNames as $name) {
            if (Schema::hasIndex('daftar_harga', $name)) {
                Schema::table('daftar_harga', function (Blueprint $table) use ($name) {
                    $table->dropUnique($name);
                });
            }
        }

        if ($isMysql && ! $hasSupplierFk()) {
            Schema::table('daftar_harga', function (Blueprint $table) {
                $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();
            });
        }

        // Backfill data existing
        DB::table('daftar_harga')
            ->whereNull('min_qty')
            ->update(['min_qty' => 1]);
    }

    public function down(): void
    {
        Schema::table('daftar_harga', function (Blueprint $table) {
            $table->dropColumn(['min_qty', 'max_qty', 'tanggal_mulai', 'tanggal_selesai', 'keterangan']);
            $table->unique(['supplier_id', 'barang_id'], 'daftar_harga_supplier_id_barang_id_unique');
        });
    }
};
