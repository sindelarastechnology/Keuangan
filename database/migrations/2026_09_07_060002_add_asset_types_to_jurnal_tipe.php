<?php

use App\Models\AkunPerkiraan;
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
                'perolehan_aset', 'penyusutan',
            ])->default('manual')->change();
        });

        $parentId = AkunPerkiraan::where('kode', '52')->value('id');
        AkunPerkiraan::updateOrCreate(
            ['kode' => '526'],
            [
                'nama' => 'Beban Penyusutan',
                'jenis' => 'beban',
                'saldo_normal' => 'debit',
                'is_header' => false,
                'parent_id' => $parentId,
            ]
        );
    }

    public function down(): void
    {
        AkunPerkiraan::where('kode', '526')->delete();

        Schema::table('jurnal_umum', function (Blueprint $table) {
            $table->enum('tipe', [
                'kas_masuk', 'kas_keluar', 'mutasi_bank', 'pembelian',
                'penjualan', 'manual', 'tutup_buku', 'hpp', 'pembayaran',
                'retur_penjualan', 'retur_pembelian', 'penyesuaian_stok',
            ])->default('manual')->change();
        });
    }
};
