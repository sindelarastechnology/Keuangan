<?php

namespace App\Services;

use App\Models\AkunPerkiraan;
use App\Models\Aset;
use App\Models\JurnalUmum;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AsetDisposisiService
{
    public const ALASAN = [
        'rusak' => 'Rusak',
        'dijual' => 'Dijual',
        'hilang' => 'Hilang',
    ];

    /**
     * Hapus aset dari daftar (rusak / dijual / hilang) sekaligus membuat
     * jurnal penghapusan. Otomatis menghitung laba/rugi saat dijual.
     */
    public static function disposisi(Aset $aset, string $alasan, ?float $hargaJual = null): JurnalUmum
    {
        if (! isset(self::ALASAN[$alasan])) {
            throw new RuntimeException('Alasan penghapusan tidak dikenal.');
        }

        if ($aset->status === 'nonaktif') {
            throw new RuntimeException('Aset '.$aset->kode.' sudah tidak aktif / sudah dihapus.');
        }

        $aset->loadSum('penyusutan', 'beban');

        $hargaPerolehan = round((float) $aset->harga_perolehan, 2);
        $akumulasi = round((float) $aset->akumulasi, 2);
        $hargaJual = round((float) ($hargaJual ?: 0), 2);

        if ($alasan === 'dijual' && $hargaJual <= 0) {
            throw new RuntimeException('Untuk aset yang dijual, harga jual harus lebih dari 0.');
        }

        $laba = round($hargaJual - ($hargaPerolehan - $akumulasi), 2);

        $keterangan = self::ALASAN[$alasan].' '.$aset->kode.' '.$aset->nama;

        $items = [];
        if ($akumulasi > 0.01) {
            $items[] = ['akun_id' => $aset->akun_akumulasi_id, 'debit' => $akumulasi, 'kredit' => 0, 'keterangan' => $keterangan];
        }
        if ($hargaJual > 0.01) {
            $items[] = ['akun_id' => self::akunKasPenerimaan($aset), 'debit' => $hargaJual, 'kredit' => 0, 'keterangan' => $keterangan];
        }
        $items[] = ['akun_id' => $aset->akun_aset_id, 'debit' => 0, 'kredit' => $hargaPerolehan, 'keterangan' => $keterangan];

        if ($laba < -0.01) {
            $akunRugi = PengaturanSistemService::akunId('rugi_lain');
            if (! $akunRugi) {
                throw new RuntimeException('Akun beban untuk kerugian aset (531) tidak ditemukan.');
            }
            $items[] = ['akun_id' => $akunRugi, 'debit' => abs($laba), 'kredit' => 0, 'keterangan' => 'Rugi penjualan '.$aset->kode];
        } elseif ($laba > 0.01) {
            $akunLaba = PengaturanSistemService::akunId('pendapatan_lain');
            if (! $akunLaba) {
                throw new RuntimeException('Akun pendapatan untuk laba aset (421) tidak ditemukan.');
            }
            $items[] = ['akun_id' => $akunLaba, 'debit' => 0, 'kredit' => $laba, 'keterangan' => 'Laba penjualan '.$aset->kode];
        }

        return DB::transaction(function () use ($aset, $items, $keterangan, $alasan, $hargaJual, $laba) {
            $jurnal = JournalService::post(
                'penghapusan_aset',
                now()->toDateString(),
                $items,
                'Penghapusan Aset '.$keterangan,
                $aset
            );

            $aset->update([
                'status' => 'nonaktif',
                'disposisi_alasan' => $alasan,
                'disposisi_harga_jual' => $hargaJual,
                'disposisi_laba' => $laba,
                'disposisi_tanggal' => now()->toDateString(),
                'keterangan' => trim(($aset->keterangan ?? '').' ['.$keterangan.']'),
            ]);

            return $jurnal;
        });
    }

    /**
     * Akun penerimaan uang saat aset dijual: pakai sumber dana aset bila
     * berupa kas/bank (termasuk akun anak hasil isolasi rekening, mis. 112x),
     * selain itu jatuh ke Kas (111).
     */
    private static function akunKasPenerimaan(Aset $aset): int
    {
        if ($aset->sumber_dana_id) {
            $akun = AkunPerkiraan::find($aset->sumber_dana_id);

            if ($akun && PengaturanSistemService::akunKasBank()->contains('id', $akun->id)) {
                return $akun->id;
            }
        }

        $kasId = PengaturanSistemService::akunId('kas');

        if (! $kasId) {
            throw new RuntimeException('Akun Kas (111) tidak ditemukan.');
        }

        return $kasId;
    }
}
