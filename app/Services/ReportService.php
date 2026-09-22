<?php

namespace App\Services;

use App\Models\AkunPerkiraan;
use App\Models\Aset;
use App\Models\JurnalItem;
use App\Models\JurnalUmum;
use App\Models\Penyusutan;

class ReportService
{
    /** Kode akun saldo laba berjalan (perkiraan 33). */
    public const KODE_LABA_RUGI = '33';

    /** Awalan kode akun aset tetap (daerah 12x). */
    public const PREFIX_ASET_TETAP = '12%';

    /**
     * Saldo akun berdasarkan rentang tanggal (debit/kredit net berdasarkan saldo_normal).
     */
    public static function saldoAkunRentang(int $akunId, string $dari, string $sampai): float
    {
        $akun = AkunPerkiraan::findOrFail($akunId);
        $debit = (float) JurnalItem::where('akun_id', $akunId)
            ->whereHas('jurnal', fn ($q) => $q->tanpaVoid()->where('is_posted', true)->whereBetween('tanggal', [$dari, $sampai]))
            ->sum('debit');
        $kredit = (float) JurnalItem::where('akun_id', $akunId)
            ->whereHas('jurnal', fn ($q) => $q->tanpaVoid()->where('is_posted', true)->whereBetween('tanggal', [$dari, $sampai]))
            ->sum('kredit');

        return $akun->saldo_normal === 'debit' ? $debit - $kredit : $kredit - $debit;
    }

    /**
     * Saldo kumulatif akun sampai tanggal tertentu (untuk neraca).
     *
     * Jika akun belum dikonfigurasi (null/0), kembalikan 0 alih-alih
     * memanggil findOrFail(0) yang selalu ModelNotFoundException.
     */
    public static function saldoAkunKumulatif(?int $akunId, string $sampai): float
    {
        if (! $akunId || $akunId <= 0) {
            return 0.0;
        }

        $akun = AkunPerkiraan::findOrFail($akunId);
        $debit = (float) JurnalItem::where('akun_id', $akunId)
            ->whereHas('jurnal', fn ($q) => $q->tanpaVoid()->where('is_posted', true)->where('tanggal', '<=', $sampai))
            ->sum('debit');
        $kredit = (float) JurnalItem::where('akun_id', $akunId)
            ->whereHas('jurnal', fn ($q) => $q->tanpaVoid()->where('is_posted', true)->where('tanggal', '<=', $sampai))
            ->sum('kredit');

        return $akun->saldo_normal === 'debit' ? $debit - $kredit : $kredit - $debit;
    }

    /**
     * Neto debit akun dalam rentang tanggal (debit - kredit), tanpa terpengaruh
     * saldo_normal. Dipakai untuk pergerakan modal kerja yang arah kasnya sudah
     * jelas secara ekonomi (mis. PPN Masukan yang dibayar mengurangi kas).
     */
    public static function debitNetoRentang(int $akunId, string $dari, string $sampai): float
    {
        if ($akunId <= 0) {
            return 0.0;
        }

        $neto = (float) JurnalItem::where('akun_id', $akunId)
            ->whereHas('jurnal', fn ($q) => $q->tanpaVoid()->where('is_posted', true)->whereBetween('tanggal', [$dari, $sampai]))
            ->sum('debit');

        return $neto - (float) JurnalItem::where('akun_id', $akunId)
            ->whereHas('jurnal', fn ($q) => $q->tanpaVoid()->where('is_posted', true)->whereBetween('tanggal', [$dari, $sampai]))
            ->sum('kredit');
    }

    /**
     * Neto kredit akun dalam rentang tanggal (kredit - debit), tanpa terpengaruh
     * saldo_normal. Dipakai untuk kewajiban yang kenaikannya menambah kas.
     */
    public static function kreditNetoRentang(int $akunId, string $dari, string $sampai): float
    {
        return -self::debitNetoRentang($akunId, $dari, $sampai);
    }

    /**
     * Data Laba Rugi dalam rentang tanggal.
     */
    public static function labaRugi(string $dari, string $sampai): array
    {
        $pendapatanAkun = AkunPerkiraan::where('jenis', 'pendapatan')->leaf()->orderBy('kode')->get();
        $bebanAkun = AkunPerkiraan::where('jenis', 'beban')->leaf()->orderBy('kode')->get();

        $pendapatan = [];
        $totalPendapatan = 0;
        foreach ($pendapatanAkun as $akun) {
            $saldo = self::saldoAkunRentang($akun->id, $dari, $sampai);
            if (abs($saldo) > 0.01) {
                $pendapatan[] = ['akun' => $akun, 'saldo' => $saldo];
                $totalPendapatan += $saldo;
            }
        }

        $beban = [];
        $totalBeban = 0;
        foreach ($bebanAkun as $akun) {
            $saldo = self::saldoAkunRentang($akun->id, $dari, $sampai);
            if (abs($saldo) > 0.01) {
                $beban[] = ['akun' => $akun, 'saldo' => $saldo];
                $totalBeban += $saldo;
            }
        }

        $labaKotor = $totalPendapatan - $totalBeban;

        return [
            'pendapatan' => $pendapatan,
            'total_pendapatan' => $totalPendapatan,
            'beban' => $beban,
            'total_beban' => $totalBeban,
            'laba_rugi' => $labaKotor,
        ];
    }

    /**
     * Data Neraca sampai tanggal tertentu.
     */
    public static function neraca(string $sampai): array
    {
        $aset = [[], 0.0];
        $kewajiban = [[], 0.0];
        $modal = [[], 0.0];

        $asetAkun = AkunPerkiraan::where('jenis', 'aset')->leaf()->orderBy('kode')->get();
        foreach ($asetAkun as $akun) {
            $saldo = self::saldoAkunKumulatif($akun->id, $sampai);
            if (abs($saldo) > 0.01) {
                // Akun aset dengan saldo_normal kredit (mis. Akumulasi Penyusutan 122)
                // adalah kontra aset: disajikan negatif agar posisi neraca benar.
                $nilai = $akun->saldo_normal === 'debit' ? $saldo : -$saldo;
                $aset[0][] = ['akun' => $akun, 'saldo' => $nilai];
                $aset[1] += $nilai;
            }
        }

        $kewajibanAkun = AkunPerkiraan::where('jenis', 'kewajiban')->leaf()->orderBy('kode')->get();
        foreach ($kewajibanAkun as $akun) {
            $saldo = self::saldoAkunKumulatif($akun->id, $sampai);
            if (abs($saldo) > 0.01) {
                $kewajiban[0][] = ['akun' => $akun, 'saldo' => $saldo];
                $kewajiban[1] += $saldo;
            }
        }

        // Akun modal biasa (misal Modal Pemilik, Laba Ditahan) kecuali Akun 33 yang dialokasikan khusus laba berjalan
        $modalAkun = AkunPerkiraan::where('jenis', 'modal')->where('kode', '!=', self::KODE_LABA_RUGI)->leaf()->orderBy('kode')->get();
        foreach ($modalAkun as $akun) {
            $saldo = self::saldoAkunKumulatif($akun->id, $sampai);
            if (abs($saldo) > 0.01) {
                $modal[0][] = ['akun' => $akun, 'saldo' => $saldo];
                $modal[1] += $saldo;
            }
        }

        // Hitung Laba / (Rugi) Periode Berjalan kumulatif sampai $sampai
        $totalPendapatan = 0.0;
        foreach (AkunPerkiraan::where('jenis', 'pendapatan')->leaf()->get() as $pAkun) {
            $totalPendapatan += self::saldoAkunKumulatif($pAkun->id, $sampai);
        }

        $totalBeban = 0.0;
        foreach (AkunPerkiraan::where('jenis', 'beban')->leaf()->get() as $bAkun) {
            $totalBeban += self::saldoAkunKumulatif($bAkun->id, $sampai);
        }

        $labaRugiBerjalan = $totalPendapatan - $totalBeban;

        if (abs($labaRugiBerjalan) > 0.01) {
            $akun33 = AkunPerkiraan::where('kode', self::KODE_LABA_RUGI)->first();
            $labelAkun = $akun33 ?: (object) ['nama' => 'Laba / (Rugi) Periode Berjalan', 'kode' => self::KODE_LABA_RUGI];
            $modal[0][] = [
                'akun' => $labelAkun,
                'saldo' => $labaRugiBerjalan,
                'is_laba_berjalan' => true,
            ];
            $modal[1] += $labaRugiBerjalan;
        }

        $totalAset = round($aset[1], 2);
        $totalKewajiban = round($kewajiban[1], 2);
        $totalModal = round($modal[1], 2);
        $totalKewajibanModal = round($totalKewajiban + $totalModal, 2);
        $selisih = round($totalAset - $totalKewajibanModal, 2);

        return [
            'aset' => $aset[0],
            'total_aset' => $totalAset,
            'kewajiban' => $kewajiban[0],
            'total_kewajiban' => $totalKewajiban,
            'modal' => $modal[0],
            'total_modal' => $totalModal,
            'total_kewajiban_modal' => $totalKewajibanModal,
            'laba_rugi_berjalan' => $labaRugiBerjalan,
            'selisih' => $selisih,
            'is_balanced' => abs($selisih) <= 0.05,
        ];
    }

    /**
     * Neraca Saldo (Trial Balance) per tanggal tertentu.
     */
    public static function neracaSaldo(string $sampai): array
    {
        $akuns = AkunPerkiraan::leaf()->orderBy('kode')->get();
        $rows = [];
        $grandDebit = 0.0;
        $grandKredit = 0.0;

        foreach ($akuns as $akun) {
            $debit = (float) JurnalItem::where('akun_id', $akun->id)
                ->whereHas('jurnal', fn ($q) => $q->tanpaVoid()->where('is_posted', true)->where('tanggal', '<=', $sampai))
                ->sum('debit');
            $kredit = (float) JurnalItem::where('akun_id', $akun->id)
                ->whereHas('jurnal', fn ($q) => $q->tanpaVoid()->where('is_posted', true)->where('tanggal', '<=', $sampai))
                ->sum('kredit');

            if ($debit == 0 && $kredit == 0) {
                continue;
            }

            $saldoDebit = 0.0;
            $saldoKredit = 0.0;
            if ($debit >= $kredit) {
                $saldoDebit = $debit - $kredit;
            } else {
                $saldoKredit = $kredit - $debit;
            }

            $grandDebit += $saldoDebit;
            $grandKredit += $saldoKredit;

            $rows[] = [
                'akun' => $akun,
                'debit' => $saldoDebit,
                'kredit' => $saldoKredit,
            ];
        }

        return [
            'rows' => $rows,
            'total_debit' => round($grandDebit, 2),
            'total_kredit' => round($grandKredit, 2),
            'is_balanced' => abs($grandDebit - $grandKredit) <= 0.05,
        ];
    }

    /**
     * Laporan Arus Kas (Cash Flow Statement) - metode tidak langsung.
     */
    public static function arusKas(string $dari, string $sampai): array
    {
        $labaRugiData = self::labaRugi($dari, $sampai);
        $labaRugi = $labaRugiData['laba_rugi'];

        $penyusutan = (float) Penyusutan::whereDate('tanggal', '>=', $dari)
            ->whereDate('tanggal', '<=', $sampai)
            ->sum('beban');

        // Setiap akun kas/bank (termasuk akun anak turunan hasil isolasi rekening)
        // dihitung sebagai posisi kas, bukan hanya dua akun terkonfigurasi.
        $kasAwalAkun = PengaturanSistemService::akunKasBank();
        $kasAwal = 0;
        foreach ($kasAwalAkun as $akun) {
            $saldoSebelum = self::saldoAkunKumulatif($akun->id, date('Y-m-d', strtotime($dari.' -1 day')));
            $kasAwal += $saldoSebelum;
        }

        $piutangAwal = self::saldoAkunKumulatif(PengaturanSistemService::akunId('piutang') ?? 0, date('Y-m-d', strtotime($dari.' -1 day')));
        $piutangAkhir = self::saldoAkunKumulatif(PengaturanSistemService::akunId('piutang') ?? 0, $sampai);
        $perubahanPiutang = $piutangAkhir - $piutangAwal;

        $persediaanAwal = self::saldoAkunKumulatif(PengaturanSistemService::akunId('persediaan') ?? 0, date('Y-m-d', strtotime($dari.' -1 day')));
        $persediaanAkhir = self::saldoAkunKumulatif(PengaturanSistemService::akunId('persediaan') ?? 0, $sampai);
        $perubahanPersediaan = $persediaanAkhir - $persediaanAwal;

        $hutangAwal = self::saldoAkunKumulatif(PengaturanSistemService::akunId('utang') ?? 0, date('Y-m-d', strtotime($dari.' -1 day')));
        $hutangAkhir = self::saldoAkunKumulatif(PengaturanSistemService::akunId('utang') ?? 0, $sampai);
        $perubahanHutang = $hutangAkhir - $hutangAwal;

        // PPN Masukan (213) & PPN Keluaran (212) adalah modal kerja operasional:
        // PPN yang dibayar/diterima melalui kas harus tercermin di aktivitas
        // operasional agar perubahan kas masuk rekonsiliasi. Perubahan dihitung
        // dari debit/kredit neto akun (tidak bergantung saldo_normal) karena
        // sifat kedua akun di data master bisa berbeda dari perilaku kas riilnya.
        $perubahanPpnMasukan = self::debitNetoRentang(PengaturanSistemService::akunId('ppn_masukan') ?? 0, $dari, $sampai);
        $perubahanPpnKeluaran = self::kreditNetoRentang(PengaturanSistemService::akunId('ppn_keluaran') ?? 0, $dari, $sampai);

        // Laba/rugi dan akumulasi dari jurnal penghapusan aset. Keuntungan dari
        // penjualan aset bukan aktivitas operasional, sehingga dikeluarkan dari
        // operasional; sisi investasi dikoreksi agar hanya menunjukkan kas riil.
        [$labaDisposisi, $akumulasiDisposisi] = self::dataDisposisi($dari, $sampai);

        $kasOperasional = $labaRugi + $penyusutan - $labaDisposisi
            - $perubahanPiutang - $perubahanPersediaan - $perubahanPpnMasukan
            + $perubahanHutang + $perubahanPpnKeluaran;

        $asetTetapAkun = AkunPerkiraan::where('kode', 'LIKE', self::PREFIX_ASET_TETAP)->where('kode', '!=', '122')->leaf()->get();
        $investasi = 0;
        foreach ($asetTetapAkun as $akun) {
            $perubahan = self::saldoAkunRentang($akun->id, $dari, $sampai);
            $investasi -= $perubahan;
        }

        // Koreksi investasi: arus kas riil disposisi adalah hasil jual (proceeds),
        // bukan pengurangan penuh harga perolehan. Karena
        //   proceeds = hargaPerolehan − akumulasi + laba
        // dan pengurangan harga perolehan sudah termuat di −Δ akun aset tetap,
        // maka tambahan koreksinya = −akumulasi + laba.
        $investasi += -$akumulasiDisposisi + $labaDisposisi;

        $kasInvestasi = $investasi;

        // Perubahan modal dari transaksi pemilik (setoran, prive); akun 33 adalah laba berjalan
        // yang sudah diperhitungkan lewat laba rugi pada aktivitas operasional.
        $modalAkun = AkunPerkiraan::where('jenis', 'modal')->where('kode', '!=', self::KODE_LABA_RUGI)->leaf()->get();
        $pendanaan = 0;
        foreach ($modalAkun as $akun) {
            $perubahan = self::saldoAkunRentang($akun->id, $dari, $sampai);
            $pendanaan += $perubahan;
        }

        $kasPendanaan = $pendanaan;

        $kasAkhir = 0;
        foreach ($kasAwalAkun as $akun) {
            $kasAkhir += self::saldoAkunKumulatif($akun->id, $sampai);
        }

        $perubahanKas = $kasOperasional + $kasInvestasi + $kasPendanaan;

        return [
            'kas_awal' => $kasAwal,
            'laba_rugi' => $labaRugi,
            'penyusutan' => $penyusutan,
            'laba_disposisi' => round($labaDisposisi, 2),
            'akumulasi_disposisi' => round($akumulasiDisposisi, 2),
            'perubahan_piutang' => $perubahanPiutang,
            'perubahan_persediaan' => $perubahanPersediaan,
            'perubahan_hutang' => $perubahanHutang,
            'perubahan_ppn_masukan' => $perubahanPpnMasukan,
            'perubahan_ppn_keluaran' => $perubahanPpnKeluaran,
            'kas_operasional' => $kasOperasional,
            'kas_investasi' => $kasInvestasi,
            'kas_pendanaan' => $kasPendanaan,
            'perubahan_kas' => $perubahanKas,
            'kas_akhir' => $kasAkhir,
            'selisih' => abs($kasAkhir - ($kasAwal + $perubahanKas)),
        ];
    }

    /**
     * Laba/rugi dan akumulasi yang dihapus dari jurnal penghasilan aset ('penghapusan_aset').
     *
     * Nilai diambil dari baris jurnal sehingga konsisten dengan pembukuan;
     * pembatalan (void) otomatis tercakup karena membalik tanda leg-nya.
     *
     * @return array{0: float, 1: float} index 0: laba (positif = laba, negatif = rugi), index 1: akumulasi yang dihapus
     */
    private static function dataDisposisi(string $dari, string $sampai): array
    {
        $akunLaba = PengaturanSistemService::akunId('pendapatan_lain');
        $akunRugi = PengaturanSistemService::akunId('rugi_lain');
        $akunAkumulasi = PengaturanSistemService::akunId('akumulasi_penyusutan');

        $jurnals = JurnalUmum::where('tipe', 'penghapusan_aset')
            ->tanpaVoid()
            ->where('is_posted', true)
            ->whereBetween('tanggal', [$dari, $sampai])
            ->with('items')
            ->get();

        $laba = 0.0;
        $akumulasi = 0.0;

        foreach ($jurnals as $jurnal) {
            foreach ($jurnal->items as $item) {
                $debit = (float) $item->debit;
                $kredit = (float) $item->kredit;

                if (in_array($item->akun_id, [$akunLaba, $akunRugi], true)) {
                    $laba += $kredit - $debit;
                } elseif ($item->akun_id === $akunAkumulasi) {
                    $akumulasi += $debit - $kredit;
                }
            }
        }

        return [$laba, $akumulasi];
    }

    /**
     * Laporan Penyusutan: perolehan, akumulasi, dan nilai buku per aset.
     */
    public static function penyusutan(string $sampai): array
    {
        $asets = Aset::query()
            ->withSum(['penyusutan' => fn ($q) => $q->whereDate('tanggal', '<=', $sampai)], 'beban')
            ->orderBy('nama')->get();

        $rows = [];
        $totalPerolehan = 0.0;
        $totalAkumulasi = 0.0;
        $totalNilaiBuku = 0.0;

        foreach ($asets as $aset) {
            $akumulasi = $aset->akumulasi;
            $nilaiBuku = $aset->nilai_buku;
            $rows[] = [
                'aset' => $aset,
                'akumulasi' => $akumulasi,
                'nilai_buku' => $nilaiBuku,
            ];
            $totalPerolehan += (float) $aset->harga_perolehan;
            $totalAkumulasi += $akumulasi;
            $totalNilaiBuku += $nilaiBuku;
        }

        return [
            'rows' => $rows,
            'total_perolehan' => round($totalPerolehan, 2),
            'total_akumulasi' => round($totalAkumulasi, 2),
            'total_nilai_buku' => round($totalNilaiBuku, 2),
        ];
    }
}
