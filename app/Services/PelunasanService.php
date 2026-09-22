<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\Rekening;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

/**
 * Satu-satunya implementasi pencatatan pelunasan piutang/hutang.
 *
 * Semua pintu masuk (tombol pada detail transaksi, menu Tagihan, shortcut
 * pada daftar penjualan/pembelian) harus memakai service ini supaya perilaku
 * dan akun yang dipakai konsisten. Validasi nominal (tidak melebihi sisa)
 * tetap dilakukan di controller pemanggil; service mencatat apa adanya.
 */
class PelunasanService
{
    /**
     * Catat pembayaran piutang customer sebagai Kas Masuk (1 item akun 113)
     * dan alokasikan ke faktur-faktur customer secara FIFO.
     */
    public static function bayarPiutang(
        Customer $customer,
        float $nominal,
        int $rekeningId,
        string $tanggal,
        ?string $keterangan = null
    ): KasMasuk {
        $nominal = round($nominal, 2);

        DB::beginTransaction();
        try {
            $rekening = Rekening::findOrFail($rekeningId);
            $akunPiutang = (int) (PengaturanSistemService::akunId('piutang') ?? 0);

            if ($akunPiutang <= 0) {
                throw new \RuntimeException('Akun piutang belum dikonfigurasi di Pengaturan Sistem.');
            }

            $kasMasuk = new KasMasuk;
            $kasMasuk->nomor = NomorGenerator::generate('KM', $tanggal);
            $kasMasuk->tanggal = $tanggal;
            $kasMasuk->rekening_id = $rekening->id;
            $kasMasuk->customer_id = $customer->id;
            $kasMasuk->keterangan = $keterangan;
            $kasMasuk->total = $nominal;
            $kasMasuk->pajak_id = null;
            $kasMasuk->pajak_nominal = 0;
            $kasMasuk->grand_total = $nominal;
            $kasMasuk->created_by = auth()->id();
            $kasMasuk->save();

            $kasMasuk->items()->create([
                'akun_id' => $akunPiutang,
                'keterangan' => $keterangan,
                'nominal' => $nominal,
            ]);

            JournalService::post('kas_masuk', $tanggal, [
                ['akun_id' => $rekening->akun_id, 'debit' => $nominal, 'kredit' => 0],
                ['akun_id' => $akunPiutang, 'debit' => 0, 'kredit' => $nominal, 'keterangan' => $keterangan],
            ], $keterangan ?? 'Pelunasan piutang '.$customer->nama, $kasMasuk);

            PiutangAttributionService::alokasikan(
                $customer,
                $nominal,
                'Pelunasan Kas Masuk '.$kasMasuk->nomor.($keterangan ? ' - '.$keterangan : ''),
                $tanggal,
                $kasMasuk->jurnal->id
            );

            DB::commit();

            return $kasMasuk;
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Catat pembayaran hutang ke supplier sebagai Kas Keluar (1 item akun 211)
     * dan alokasikan ke faktur-faktur supplier secara FIFO.
     */
    public static function bayarHutang(
        Supplier $supplier,
        float $nominal,
        int $rekeningId,
        string $tanggal,
        ?string $keterangan = null
    ): KasKeluar {
        $nominal = round($nominal, 2);

        DB::beginTransaction();
        try {
            $rekening = Rekening::findOrFail($rekeningId);
            $akunUtang = (int) (PengaturanSistemService::akunId('utang') ?? 0);

            if ($akunUtang <= 0) {
                throw new \RuntimeException('Akun utang belum dikonfigurasi di Pengaturan Sistem.');
            }

            $kasKeluar = new KasKeluar;
            $kasKeluar->nomor = NomorGenerator::generate('KK', $tanggal);
            $kasKeluar->tanggal = $tanggal;
            $kasKeluar->rekening_id = $rekening->id;
            $kasKeluar->supplier_id = $supplier->id;
            $kasKeluar->keterangan = $keterangan;
            $kasKeluar->total = $nominal;
            $kasKeluar->pajak_id = null;
            $kasKeluar->pajak_nominal = 0;
            $kasKeluar->grand_total = $nominal;
            $kasKeluar->created_by = auth()->id();
            $kasKeluar->save();

            $kasKeluar->items()->create([
                'akun_id' => $akunUtang,
                'keterangan' => $keterangan,
                'nominal' => $nominal,
            ]);

            JournalService::post('kas_keluar', $tanggal, [
                ['akun_id' => $akunUtang, 'debit' => $nominal, 'kredit' => 0, 'keterangan' => $keterangan],
                ['akun_id' => $rekening->akun_id, 'debit' => 0, 'kredit' => $nominal],
            ], $keterangan ?? 'Pelunasan hutang '.$supplier->nama, $kasKeluar);

            HutangAttributionService::alokasikan(
                $supplier,
                $nominal,
                'Pelunasan Kas Keluar '.$kasKeluar->nomor.($keterangan ? ' - '.$keterangan : ''),
                $tanggal,
                $kasKeluar->jurnal->id
            );

            DB::commit();

            return $kasKeluar;
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
