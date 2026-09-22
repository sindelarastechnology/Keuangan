<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel data milik tenant (pendamping kolom user_id).
     */
    private const TABLES = [
        'akun_perkiraan',
        'asets',
        'asset_templates',
        'barang',
        'bb_hutang',
        'bb_persediaan',
        'bb_piutang',
        'customers',
        'daftar_harga',
        'daftar_harga_riwayat',
        'gudangs',
        'jurnal_items',
        'jurnal_umum',
        'kas_keluar',
        'kas_keluar_items',
        'kas_masuk',
        'kas_masuk_items',
        'kategori',
        'mutasi_bank',
        'pajak',
        'pembelian_items',
        'pembelians',
        'pengaturan',
        'penjualan_items',
        'penjualans',
        'penyusutan',
        'periode_akuntansi',
        'perubahan_stok',
        'perubahan_stok_items',
        'rekenings',
        'retur_pembelian',
        'retur_pembelian_items',
        'retur_penjualan',
        'retur_penjualan_items',
        'satuan',
        'stok_gudang',
        'stok_opname',
        'stok_opname_items',
        'suppliers',
        'transfer_gudang',
        'transfer_gudang_items',
        'tutup_buku',
        'tutup_buku_tahunan',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->unsignedBigInteger('user_id')->nullable()->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('user_id');
            });
        }
    }
};
