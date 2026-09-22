<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Konversi unik per-tenant: (user_id, kolom...)
     */
    private const UNIQUES = [
        ['table' => 'akun_perkiraan', 'old' => 'akun_perkiraan_kode_unique', 'old_cols' => ['kode'], 'cols' => ['user_id', 'kode']],
        ['table' => 'suppliers', 'old' => 'suppliers_kode_unique', 'old_cols' => ['kode'], 'cols' => ['user_id', 'kode']],
        ['table' => 'customers', 'old' => 'customers_kode_unique', 'old_cols' => ['kode'], 'cols' => ['user_id', 'kode']],
        ['table' => 'barang', 'old' => 'barang_kode_unique', 'old_cols' => ['kode'], 'cols' => ['user_id', 'kode']],
        ['table' => 'satuan', 'old' => 'satuan_nama_unique', 'old_cols' => ['nama'], 'cols' => ['user_id', 'nama']],
        ['table' => 'kategori', 'old' => 'kategori_nama_unique', 'old_cols' => ['nama'], 'cols' => ['user_id', 'nama']],
        ['table' => 'asets', 'old' => 'asets_kode_unique', 'old_cols' => ['kode'], 'cols' => ['user_id', 'kode']],
        ['table' => 'asset_templates', 'old' => 'asset_templates_nama_kategori_unique', 'old_cols' => ['nama_kategori'], 'cols' => ['user_id', 'nama_kategori']],
        ['table' => 'gudangs', 'old' => 'gudangs_kode_unique', 'old_cols' => ['kode'], 'cols' => ['user_id', 'kode']],
        ['table' => 'pengaturan', 'old' => 'pengaturan_key_unique', 'old_cols' => ['key'], 'cols' => ['user_id', 'key']],
        ['table' => 'periode_akuntansi', 'old' => 'periode_akuntansi_kode_unique', 'old_cols' => ['kode'], 'cols' => ['user_id', 'kode']],
        ['table' => 'periode_akuntansi', 'old' => 'periode_akuntansi_bulan_tahun_unique', 'old_cols' => ['bulan', 'tahun'], 'cols' => ['user_id', 'bulan', 'tahun']],
        ['table' => 'jurnal_umum', 'old' => 'jurnal_umum_nomor_unique', 'old_cols' => ['nomor'], 'cols' => ['user_id', 'nomor']],
        ['table' => 'penjualans', 'old' => 'penjualans_nomor_unique', 'old_cols' => ['nomor'], 'cols' => ['user_id', 'nomor']],
        ['table' => 'pembelians', 'old' => 'pembelians_nomor_unique', 'old_cols' => ['nomor'], 'cols' => ['user_id', 'nomor']],
        ['table' => 'kas_masuk', 'old' => 'kas_masuk_nomor_unique', 'old_cols' => ['nomor'], 'cols' => ['user_id', 'nomor']],
        ['table' => 'kas_keluar', 'old' => 'kas_keluar_nomor_unique', 'old_cols' => ['nomor'], 'cols' => ['user_id', 'nomor']],
        ['table' => 'mutasi_bank', 'old' => 'mutasi_bank_nomor_unique', 'old_cols' => ['nomor'], 'cols' => ['user_id', 'nomor']],
        ['table' => 'stok_opname', 'old' => 'stok_opname_nomor_unique', 'old_cols' => ['nomor'], 'cols' => ['user_id', 'nomor']],
        ['table' => 'perubahan_stok', 'old' => 'perubahan_stok_nomor_unique', 'old_cols' => ['nomor'], 'cols' => ['user_id', 'nomor']],
        ['table' => 'transfer_gudang', 'old' => 'transfer_gudang_nomor_unique', 'old_cols' => ['nomor'], 'cols' => ['user_id', 'nomor']],
        ['table' => 'retur_penjualan', 'old' => 'retur_penjualan_nomor_unique', 'old_cols' => ['nomor'], 'cols' => ['user_id', 'nomor']],
        ['table' => 'retur_pembelian', 'old' => 'retur_pembelian_nomor_unique', 'old_cols' => ['nomor'], 'cols' => ['user_id', 'nomor']],
        ['table' => 'tutup_buku_tahunan', 'old' => 'tutup_buku_tahunan_tahun_unique', 'old_cols' => ['tahun'], 'cols' => ['user_id', 'tahun']],
        ['table' => 'penyusutan', 'old' => 'penyusutan_aset_id_periode_unique', 'old_cols' => ['aset_id', 'periode'], 'cols' => ['user_id', 'aset_id', 'periode']],
        ['table' => 'stok_gudang', 'old' => 'stok_gudang_barang_id_gudang_id_unique', 'old_cols' => ['barang_id', 'gudang_id'], 'cols' => ['user_id', 'barang_id', 'gudang_id']],
    ];

    /**
     * Kolom FK yang memakai unique lama sebagai index-nya (MySQL 1553):
     * saat unique diganti jadi composite, index FK perlu dibuat ulang.
     */
    private const FK_COLUMN_INDEXES = [
        'penyusutan' => ['aset_id'],
        'stok_gudang' => ['barang_id', 'gudang_id'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::UNIQUES as $unique) {
            $name = $unique['table'].'_'.implode('_', $unique['cols']).'_unique';

            // Pastikan index FK ada sebelum unique lama di-drop (MySQL 1553).
            foreach (self::FK_COLUMN_INDEXES[$unique['table']] ?? [] as $column) {
                $indexName = $unique['table'].'_'.$column.'_index';
                if (Schema::hasColumn($unique['table'], $column)
                    && ! Schema::hasIndex($unique['table'], $indexName)) {
                    Schema::table($unique['table'], function (Blueprint $table) use ($column, $indexName) {
                        $table->index($column, $indexName);
                    });
                }
            }

            if (Schema::hasIndex($unique['table'], $unique['old'])) {
                Schema::table($unique['table'], function (Blueprint $table) use ($unique) {
                    $table->dropUnique($unique['old']);
                });
            }

            if (! Schema::hasIndex($unique['table'], $name)) {
                Schema::table($unique['table'], function (Blueprint $table) use ($unique, $name) {
                    $table->unique($unique['cols'], $name);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::UNIQUES as $unique) {
            $name = $unique['table'].'_'.implode('_', $unique['cols']).'_unique';

            if (Schema::hasIndex($unique['table'], $name)) {
                Schema::table($unique['table'], function (Blueprint $table) use ($name) {
                    $table->dropUnique($name);
                });
            }

            if (! Schema::hasIndex($unique['table'], $unique['old'])) {
                Schema::table($unique['table'], function (Blueprint $table) use ($unique) {
                    $table->unique($unique['old_cols'], $unique['old']);
                });
            }
        }
    }
};
