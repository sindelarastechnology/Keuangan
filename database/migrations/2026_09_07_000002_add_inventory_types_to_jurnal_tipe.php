<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurnal_umum', function (Blueprint $table) {
            $table->enum('tipe', [
                'kas_masuk', 'kas_keluar', 'mutasi_bank', 'pembelian',
                'penjualan', 'manual', 'tutup_buku', 'hpp', 'pembayaran',
                'retur_penjualan', 'retur_pembelian', 'penyesuaian_stok',
            ])->default('manual')->change();
        });
    }

    public function down(): void
    {
        Schema::table('jurnal_umum', function (Blueprint $table) {
            $table->enum('tipe', [
                'kas_masuk', 'kas_keluar', 'mutasi_bank', 'pembelian',
                'penjualan', 'manual', 'tutup_buku', 'hpp', 'pembayaran',
            ])->default('manual')->change();
        });
    }
};
