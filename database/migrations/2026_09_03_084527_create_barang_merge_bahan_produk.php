<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create barang table with enriched columns
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 150);
            $table->string('satuan', 20)->nullable();
            $table->string('kategori', 100)->nullable();
            $table->string('merek', 100)->nullable();
            $table->string('ukuran', 50)->nullable();
            $table->string('warna', 50)->nullable();
            $table->enum('tipe', ['barang', 'jasa'])->default('barang');
            $table->decimal('stok', 18, 2)->default(0);
            $table->decimal('harga_avg', 18, 2)->default(0);
            $table->decimal('harga_beli', 18, 2)->default(0);
            $table->decimal('harga_jual', 18, 2)->default(0);
            $table->decimal('min_stok', 18, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // 2. Backfill from bahan_baku (preserve IDs)
        DB::statement('INSERT INTO barang (id, kode, nama, satuan, stok, harga_avg, harga_beli, harga_jual, min_stok, tipe, keterangan, is_aktif, created_at, updated_at)
            SELECT id, kode, nama, satuan, stok, harga_avg, 0, 0, 0, "barang", keterangan, is_aktif, created_at, updated_at FROM bahan_baku');

        // 3. Backfill from produk (remap IDs to avoid collision)
        $maxBahanId = DB::table('bahan_baku')->max('id') ?? 0;
        $offset = $maxBahanId;
        DB::statement('INSERT INTO barang (id, kode, nama, satuan, stok, harga_avg, harga_beli, harga_jual, min_stok, tipe, keterangan, is_aktif, created_at, updated_at)
            SELECT id + '.$offset.', kode, nama, satuan, stok, harga_avg, 0, harga_jual, 0, tipe, keterangan, is_aktif, created_at, updated_at FROM produk');

        // Build produk_id -> new barang_id mapping for later FK backfill
        $produkMap = [];
        $produkRows = DB::table('produk')->select('id')->get();
        foreach ($produkRows as $row) {
            $produkMap[$row->id] = $row->id + $offset;
        }

        // 4. pembelian_items: add barang_id, backfill from bahan_baku_id, drop bahan_baku_id
        Schema::table('pembelian_items', function (Blueprint $table) {
            $table->foreignId('barang_id')->nullable()->constrained('barang')->nullOnDelete();
        });
        DB::statement('UPDATE pembelian_items SET barang_id = bahan_baku_id');
        Schema::table('pembelian_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bahan_baku_id');
        });

        // 5. daftar_harga: add barang_id, backfill from bahan_baku_id, drop bahan_baku_id
        Schema::table('daftar_harga', function (Blueprint $table) {
            $table->foreignId('barang_id')->nullable()->constrained('barang')->cascadeOnDelete();
        });
        DB::statement('UPDATE daftar_harga SET barang_id = bahan_baku_id');
        // MySQL 1553: unique (supplier_id, bahan_baku_id) dipakai sebagai index FK
        // (FK supplier_id memakai prefix kiri index). DROP INDEX ditolak selama FK
        // masih ada — SET FOREIGN_KEY_CHECKS=0 tidak melewati aturan ini. Solusinya:
        // drop FK supplier_id & bahan_baku_id dulu, drop unique, baru drop kolom.
        // FK supplier_id langsung dibuat ulang agar migration 09_05_100000 yang
        // me-drop & menambahkan ulang FK supplier_id tetap berjalan tanpa error.
        $isMysql = DB::connection()->getDriverName() === 'mysql';
        if ($isMysql) {
            Schema::table('daftar_harga', function (Blueprint $table) {
                $table->dropForeign(['supplier_id']);
                $table->dropForeign(['bahan_baku_id']);
                $table->dropUnique(['supplier_id', 'bahan_baku_id']);
                $table->dropColumn('bahan_baku_id');
                $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();
            });
        } else {
            // SQLite: unique index harus di-drop lebih dulu, lalu FK + kolom (rebuild konsumsi index).
            Schema::table('daftar_harga', function (Blueprint $table) {
                $table->dropUnique(['supplier_id', 'bahan_baku_id']);
            });
            Schema::table('daftar_harga', function (Blueprint $table) {
                $table->dropConstrainedForeignId('bahan_baku_id');
            });
        }

        // 6. bb_persediaan: add barang_id, backfill from bahan_baku_id, drop bahan_baku_id
        Schema::table('bb_persediaan', function (Blueprint $table) {
            $table->foreignId('barang_id')->nullable()->constrained('barang')->nullOnDelete();
        });
        DB::statement('UPDATE bb_persediaan SET barang_id = bahan_baku_id');
        Schema::table('bb_persediaan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bahan_baku_id');
        });

        // 7. penjualan_items: add barang_id, backfill from produk_id using mapping, drop produk_id
        Schema::table('penjualan_items', function (Blueprint $table) {
            $table->foreignId('barang_id')->nullable()->constrained('barang')->nullOnDelete();
        });
        foreach ($produkMap as $oldId => $newId) {
            DB::statement("UPDATE penjualan_items SET barang_id = {$newId} WHERE produk_id = {$oldId}");
        }
        Schema::table('penjualan_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('produk_id');
        });

        // 8. Drop old tables
        Schema::dropIfExists('produk');
        Schema::dropIfExists('bahan_baku');
    }

    public function down(): void
    {
        // This migration is irreversible due to data merging and FK remapping
        // To rollback, you would need to recreate bahan_baku and produk tables
        // and restore data from barang, which is complex and not implemented.
        // Instead, we rely on database backups for rollback.
    }
};
