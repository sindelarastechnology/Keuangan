<?php

namespace App\Http\Controllers\Akuntansi;

use App\Http\Controllers\Controller;
use App\Models\AkunPerkiraan;
use App\Models\PeriodeAkuntansi;
use App\Models\TutupBukuTahunan;
use App\Services\JournalService;
use App\Services\PengaturanSistemService;
use App\Services\PeriodeService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TutupBukuTahunanController extends Controller
{
    public function index()
    {
        $tahunClosed = TutupBukuTahunan::pluck('tahun')->map(fn ($t) => (int) $t)->toArray();
        $tahunPeriode = PeriodeAkuntansi::query()
            ->orderByDesc('tahun')
            ->limit(50)
            ->pluck('tahun')
            ->map(fn ($t) => (int) $t)
            ->unique()
            ->toArray();

        $tahunList = array_values(array_unique(array_merge($tahunClosed, $tahunPeriode, [(int) now()->format('Y')])));
        rsort($tahunList);

        $tahunan = [];
        foreach ($tahunList as $tahun) {
            $tutup = TutupBukuTahunan::where('tahun', (string) $tahun)->first();
            $periode = PeriodeAkuntansi::where('tahun', (string) $tahun)->get();
            $jumlahPeriode = $periode->count();
            $jumlahTertutup = $periode->where('is_closed', true)->count();
            $jumlahTerkunci = $periode->where('is_locked', true)->count();

            $ringkasan = null;
            if (! $tutup && $jumlahPeriode > 0 && $periode->where('is_closed', true)->isEmpty()) {
                $dari = $tahun.'-01-01';
                $sampai = $tahun.'-12-31';
                $ringkasan = ReportService::labaRugi($dari, $sampai);
                $ringkasan['dari'] = $dari;
                $ringkasan['sampai'] = $sampai;
            }

            $tahunan[] = [
                'tahun' => $tahun,
                'tutup' => $tutup,
                'jumlah_periode' => $jumlahPeriode,
                'jumlah_tertutup' => $jumlahTertutup,
                'jumlah_terkunci' => $jumlahTerkunci,
                'ringkasan' => $ringkasan,
            ];
        }

        return view('akuntansi.tutup-buku-tahunan.index', compact('tahunan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|digits:4|integer',
            'keterangan' => 'nullable|string',
        ]);

        $tahun = $request->integer('tahun');

        DB::beginTransaction();
        try {
            if (TutupBukuTahunan::where('tahun', $tahun)->exists()) {
                throw new RuntimeException("Tutup buku tahunan untuk tahun {$tahun} sudah pernah dilakukan.");
            }

            PeriodeService::siapkanPerTahun($tahun);

            $periode = PeriodeAkuntansi::where('tahun', (string) $tahun)->get();
            if ($periode->count() < 12) {
                throw new RuntimeException('Periode tahun '.$tahun.' belum lengkap.');
            }

            if ($periode->where('is_closed', true)->isNotEmpty()) {
                throw new RuntimeException('Terdapat periode bulan yang sudah ditutup. Tutup buku tahunan hanya bisa dilakukan jika belum ada periode yang ditutup per bulan.');
            }

            $dari = $tahun.'-01-01';
            $sampai = $tahun.'-12-31';

            $labaRugi = ReportService::labaRugi($dari, $sampai);
            $labaBersih = $labaRugi['laba_rugi'];

            // 1. Jurnal penutup tahunan: pindahkan seluruh pendapatan & beban ke laba ditahan
            $akunLabaDitahan = PengaturanSistemService::akunId('laba_ditahan');
            if (! $akunLabaDitahan) {
                throw new RuntimeException('Akun Laba Ditahan (kode 32) belum tersedia di Master Akun. Lengkapi akun tersebut sebelum menutup buku.');
            }

            $jurnalItems = [];

            foreach (AkunPerkiraan::where('jenis', 'pendapatan')->leaf()->get() as $akun) {
                $saldo = ReportService::saldoAkunRentang($akun->id, $dari, $sampai);
                if (abs($saldo) > 0.01) {
                    $jurnalItems[] = $saldo > 0
                        ? ['akun_id' => $akun->id, 'debit' => $saldo, 'kredit' => 0]
                        : ['akun_id' => $akun->id, 'debit' => 0, 'kredit' => abs($saldo)];
                }
            }

            foreach (AkunPerkiraan::where('jenis', 'beban')->leaf()->get() as $akun) {
                $saldo = ReportService::saldoAkunRentang($akun->id, $dari, $sampai);
                if (abs($saldo) > 0.01) {
                    $jurnalItems[] = $saldo > 0
                        ? ['akun_id' => $akun->id, 'debit' => 0, 'kredit' => $saldo]
                        : ['akun_id' => $akun->id, 'debit' => abs($saldo), 'kredit' => 0];
                }
            }

            $totalDebit = round(array_sum(array_column($jurnalItems, 'debit')), 2);
            $totalKredit = round(array_sum(array_column($jurnalItems, 'kredit')), 2);
            if ($totalDebit > $totalKredit) {
                $jurnalItems[] = ['akun_id' => $akunLabaDitahan, 'debit' => 0, 'kredit' => $totalDebit - $totalKredit];
            } elseif ($totalKredit > $totalDebit) {
                $jurnalItems[] = ['akun_id' => $akunLabaDitahan, 'debit' => $totalKredit - $totalDebit, 'kredit' => 0];
            }

            if (count($jurnalItems) > 1) {
                JournalService::post('tutup_buku', $sampai, $jurnalItems, 'Tutup Buku Tahunan '.$tahun.($request->keterangan ? ' - '.$request->keterangan : ''));
            }

            // 2. Simpan catatan tutup buku tahunan
            TutupBukuTahunan::create([
                'tahun' => (string) $tahun,
                'tanggal' => now()->toDateString(),
                'laba_rugi' => $labaBersih,
                'total_pendapatan' => $labaRugi['total_pendapatan'],
                'total_beban' => $labaRugi['total_beban'],
                'keterangan' => $request->keterangan,
            ]);

            // 3. Tutup seluruh periode bulan pada tahun tersebut
            foreach ($periode as $p) {
                $p->update([
                    'is_open' => false,
                    'is_closed' => true,
                    'tanggal_tutup' => now()->toDateString(),
                ]);
            }

            DB::commit();

            $status = $labaBersih >= 0 ? 'keuntungan' : 'kerugian';

            return redirect()->route('tutup-buku-tahunan.index')
                ->with('success', "Tutup buku tahunan {$tahun} berhasil dilakukan dengan {$status} ".formatRupiah(abs($labaBersih)).'.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
