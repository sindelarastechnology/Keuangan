<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Backup seluruh data existing ke pemilik pertama (id terkecil).
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
        DB::transaction(function () {
            $ownerId = (int) DB::table('users')->min('id');

            if ($ownerId <= 0) {
                return;
            }

            foreach (self::TABLES as $table) {
                if (Schema::hasColumn($table, 'user_id')) {
                    DB::table($table)->whereNull('user_id')->update(['user_id' => $ownerId]);
                }
            }

            if ($ownerId > 0 && Schema::hasColumn('users', 'email_verified_at')) {
                DB::table('users')->where('id', $ownerId)->update(['email_verified_at' => now()]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
