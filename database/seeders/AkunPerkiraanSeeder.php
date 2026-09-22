<?php

namespace Database\Seeders;

use App\Models\AkunPerkiraan;
use Illuminate\Database\Seeder;

class AkunPerkiraanSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            // === ASET ===
            ['kode' => '1', 'nama' => 'ASET', 'jenis' => 'aset', 'saldo_normal' => 'debit', 'is_header' => true, 'parent' => null],
            ['kode' => '11', 'nama' => 'Aset Lancar', 'jenis' => 'aset', 'saldo_normal' => 'debit', 'is_header' => true, 'parent' => '1'],
            ['kode' => '111', 'nama' => 'Kas', 'jenis' => 'aset', 'saldo_normal' => 'debit', 'is_header' => false, 'parent' => '11'],
            ['kode' => '112', 'nama' => 'Bank', 'jenis' => 'aset', 'saldo_normal' => 'debit', 'is_header' => false, 'parent' => '11'],
            ['kode' => '113', 'nama' => 'Piutang Dagang', 'jenis' => 'aset', 'saldo_normal' => 'debit', 'is_header' => false, 'parent' => '11'],
            ['kode' => '114', 'nama' => 'Persediaan Bahan Baku', 'jenis' => 'aset', 'saldo_normal' => 'debit', 'is_header' => false, 'parent' => '11'],
            ['kode' => '115', 'nama' => 'Persediaan Barang Dagang', 'jenis' => 'aset', 'saldo_normal' => 'debit', 'is_header' => false, 'parent' => '11'],
            ['kode' => '12', 'nama' => 'Aset Tetap', 'jenis' => 'aset', 'saldo_normal' => 'debit', 'is_header' => true, 'parent' => '1'],
            ['kode' => '121', 'nama' => 'Peralatan', 'jenis' => 'aset', 'saldo_normal' => 'debit', 'is_header' => false, 'parent' => '12'],
            ['kode' => '122', 'nama' => 'Akomulasi Penyusutan', 'jenis' => 'aset', 'saldo_normal' => 'kredit', 'is_header' => false, 'parent' => '12'],

            // === KEWAJIBAN ===
            ['kode' => '2', 'nama' => 'KEWAJIBAN', 'jenis' => 'kewajiban', 'saldo_normal' => 'kredit', 'is_header' => true, 'parent' => null],
            ['kode' => '21', 'nama' => 'Kewajiban Lancar', 'jenis' => 'kewajiban', 'saldo_normal' => 'kredit', 'is_header' => true, 'parent' => '2'],
            ['kode' => '211', 'nama' => 'Utang Usaha', 'jenis' => 'kewajiban', 'saldo_normal' => 'kredit', 'is_header' => false, 'parent' => '21'],
            ['kode' => '212', 'nama' => 'Utang PPN Keluaran', 'jenis' => 'kewajiban', 'saldo_normal' => 'kredit', 'is_header' => false, 'parent' => '21'],
            ['kode' => '213', 'nama' => 'PPN Masukan', 'jenis' => 'kewajiban', 'saldo_normal' => 'kredit', 'is_header' => false, 'parent' => '21'],

            // === MODAL ===
            ['kode' => '3', 'nama' => 'MODAL', 'jenis' => 'modal', 'saldo_normal' => 'kredit', 'is_header' => true, 'parent' => null],
            ['kode' => '31', 'nama' => 'Modal', 'jenis' => 'modal', 'saldo_normal' => 'kredit', 'is_header' => false, 'parent' => '3'],
            ['kode' => '32', 'nama' => 'Laba Ditahan', 'jenis' => 'modal', 'saldo_normal' => 'kredit', 'is_header' => false, 'parent' => '3'],
            ['kode' => '33', 'nama' => 'Laba / Rugi Berjalan', 'jenis' => 'modal', 'saldo_normal' => 'kredit', 'is_header' => false, 'parent' => '3'],

            // === PENDAPATAN ===
            ['kode' => '4', 'nama' => 'PENDAPATAN', 'jenis' => 'pendapatan', 'saldo_normal' => 'kredit', 'is_header' => true, 'parent' => null],
            ['kode' => '41', 'nama' => 'Pendapatan Usaha', 'jenis' => 'pendapatan', 'saldo_normal' => 'kredit', 'is_header' => true, 'parent' => '4'],
            ['kode' => '411', 'nama' => 'Penjualan Barang', 'jenis' => 'pendapatan', 'saldo_normal' => 'kredit', 'is_header' => false, 'parent' => '41'],
            ['kode' => '412', 'nama' => 'Pendapatan Jasa', 'jenis' => 'pendapatan', 'saldo_normal' => 'kredit', 'is_header' => false, 'parent' => '41'],
            ['kode' => '42', 'nama' => 'Pendapatan Lain', 'jenis' => 'pendapatan', 'saldo_normal' => 'kredit', 'is_header' => true, 'parent' => '4'],
            ['kode' => '421', 'nama' => 'Pendapatan Lain-lain', 'jenis' => 'pendapatan', 'saldo_normal' => 'kredit', 'is_header' => false, 'parent' => '42'],

            // === BEBAN ===
            ['kode' => '5', 'nama' => 'BEBAN', 'jenis' => 'beban', 'saldo_normal' => 'debit', 'is_header' => true, 'parent' => null],
            ['kode' => '51', 'nama' => 'Harga Pokok Penjualan', 'jenis' => 'beban', 'saldo_normal' => 'debit', 'is_header' => false, 'parent' => '5'],
            ['kode' => '52', 'nama' => 'Beban Operasional', 'jenis' => 'beban', 'saldo_normal' => 'debit', 'is_header' => true, 'parent' => '5'],
            ['kode' => '521', 'nama' => 'Beban Gaji', 'jenis' => 'beban', 'saldo_normal' => 'debit', 'is_header' => false, 'parent' => '52'],
            ['kode' => '522', 'nama' => 'Beban Sewa', 'jenis' => 'beban', 'saldo_normal' => 'debit', 'is_header' => false, 'parent' => '52'],
            ['kode' => '523', 'nama' => 'Beban Listrik, Air & Telepon', 'jenis' => 'beban', 'saldo_normal' => 'debit', 'is_header' => false, 'parent' => '52'],
            ['kode' => '524', 'nama' => 'Beban Transportasi', 'jenis' => 'beban', 'saldo_normal' => 'debit', 'is_header' => false, 'parent' => '52'],
            ['kode' => '525', 'nama' => 'Beban Perlengkapan', 'jenis' => 'beban', 'saldo_normal' => 'debit', 'is_header' => false, 'parent' => '52'],
            ['kode' => '526', 'nama' => 'Beban Penyusutan', 'jenis' => 'beban', 'saldo_normal' => 'debit', 'is_header' => false, 'parent' => '52'],
            ['kode' => '53', 'nama' => 'Beban Lain-lain', 'jenis' => 'beban', 'saldo_normal' => 'debit', 'is_header' => true, 'parent' => '5'],
            ['kode' => '531', 'nama' => 'Beban Lain-lain', 'jenis' => 'beban', 'saldo_normal' => 'debit', 'is_header' => false, 'parent' => '53'],
        ];

        foreach ($akun as $item) {
            $parentId = null;
            if ($item['parent']) {
                $parentId = AkunPerkiraan::where('kode', $item['parent'])->value('id');
            }
            AkunPerkiraan::updateOrCreate(
                ['kode' => $item['kode']],
                [
                    'nama' => $item['nama'],
                    'jenis' => $item['jenis'],
                    'saldo_normal' => $item['saldo_normal'],
                    'is_header' => $item['is_header'],
                    'parent_id' => $parentId,
                ]
            );
        }
    }
}
