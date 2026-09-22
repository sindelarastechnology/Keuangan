<?php

namespace App\Http\Controllers\Akuntansi;

use App\Http\Controllers\Controller;
use App\Models\AkunPerkiraan;
use App\Models\TutupBuku;
use App\Services\JournalService;
use App\Services\PengaturanSistemService;
use App\Services\PeriodeService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TutupBukuController extends Controller
{
    public function index()
    {
        $periode = PeriodeService::aktif();
        $riwayat = TutupBuku::with('periode')->orderByDesc('id')->get();

        $ringkasan = null;
        if ($periode && ! $periode->is_closed) {
            [$dari, $sampai] = PeriodeService::rentang($periode);
            $ringkasan = ReportService::labaRugi($dari, $sampai);
            $ringkasan['dari'] = $dari;
            $ringkasan['sampai'] = $sampai;
        }

        return view('akuntansi.tutup-buku.index', compact('periode', 'riwayat', 'ringkasan'));
    }

    public function store(Request $request)
    {
        $request->validate(['keterangan' => 'nullable|string']);

        $periode = PeriodeService::aktif();

        if (! $periode) {
            return back()->with('error', 'Tidak ada periode aktif. Buka periode terlebih dahulu.');
        }

        if ($periode->is_closed) {
            return back()->with('error', 'Periode ini sudah ditutup.');
        }

        [$dari, $sampai] = PeriodeService::rentang($periode);
        $labaRugi = ReportService::labaRugi($dari, $sampai);
        $labaBersih = $labaRugi['laba_rugi'];

        DB::beginTransaction();
        try {
            // 1. Jurnal penutup: pindahkan total pendapatan & beban ke laba ditahan
            $akunLabaDitahan = PengaturanSistemService::akunId('laba_ditahan');

            if (! $akunLabaDitahan) {
                throw new \RuntimeException('Akun Laba Ditahan (kode 32) belum tersedia di Master Akun. Lengkapi akun tersebut sebelum menutup buku.');
            }

            $jurnalItems = [];

            // Tutup pendapatan (saldo normal kredit): sisi debit, kecuali saldo kontra (negatif)
            foreach (AkunPerkiraan::where('jenis', 'pendapatan')->leaf()->get() as $akun) {
                $saldo = ReportService::saldoAkunRentang($akun->id, $dari, $sampai);
                if (abs($saldo) > 0.01) {
                    $jurnalItems[] = $saldo > 0
                        ? ['akun_id' => $akun->id, 'debit' => $saldo, 'kredit' => 0]
                        : ['akun_id' => $akun->id, 'debit' => 0, 'kredit' => abs($saldo)];
                }
            }
            // Tutup beban (saldo normal debit): sisi kredit, kecuali saldo kontra (negatif)
            foreach (AkunPerkiraan::where('jenis', 'beban')->leaf()->get() as $akun) {
                $saldo = ReportService::saldoAkunRentang($akun->id, $dari, $sampai);
                if (abs($saldo) > 0.01) {
                    $jurnalItems[] = $saldo > 0
                        ? ['akun_id' => $akun->id, 'debit' => 0, 'kredit' => $saldo]
                        : ['akun_id' => $akun->id, 'debit' => abs($saldo), 'kredit' => 0];
                }
            }

            // Penyeimbang: laba/rugi bersih dialokasikan ke laba ditahan
            $totalDebit = round(array_sum(array_column($jurnalItems, 'debit')), 2);
            $totalKredit = round(array_sum(array_column($jurnalItems, 'kredit')), 2);
            if ($totalDebit > $totalKredit) {
                $jurnalItems[] = ['akun_id' => $akunLabaDitahan, 'debit' => 0, 'kredit' => $totalDebit - $totalKredit];
            } elseif ($totalKredit > $totalDebit) {
                $jurnalItems[] = ['akun_id' => $akunLabaDitahan, 'debit' => $totalKredit - $totalDebit, 'kredit' => 0];
            }

            if (count($jurnalItems) > 1) {
                JournalService::post('tutup_buku', $sampai, $jurnalItems, 'Tutup Buku '.$periode->label.($request->keterangan ? ' - '.$request->keterangan : ''));
            }

            // 2. Simpan catatan tutup buku
            TutupBuku::create([
                'periode_id' => $periode->id,
                'tanggal' => now()->toDateString(),
                'laba_rugi' => $labaBersih,
                'total_pendapatan' => $labaRugi['total_pendapatan'],
                'total_beban' => $labaRugi['total_beban'],
                'keterangan' => $request->keterangan,
            ]);

            // 3. Tutup periode
            $periode->update([
                'is_open' => false,
                'is_closed' => true,
                'tanggal_tutup' => now()->toDateString(),
            ]);

            DB::commit();

            $status = $labaBersih >= 0 ? 'keuntungan' : 'kerugian';

            return redirect()->route('tutup-buku.index')->with('success', "Periode {$periode->label} berhasil ditutup dengan {$status} ".formatRupiah(abs($labaBersih)).'.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }
}
