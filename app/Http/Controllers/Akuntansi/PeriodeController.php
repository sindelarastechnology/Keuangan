<?php

namespace App\Http\Controllers\Akuntansi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Models\PeriodeAkuntansi;
use App\Services\PeriodeService;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'periode_kolom';

    public const KOLOM_OPTIONS = [
        'label' => 'Periode',
        'status' => 'Status',
        'buka' => 'Dibuka',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['label', 'status', 'buka'];

    public const KOLOM_WAJIB = ['aksi'];

    public function index(Request $request)
    {
        $tahun = $request->filled('tahun') ? (int) $request->tahun : now()->year;
        $periodes = PeriodeAkuntansi::where('tahun', $tahun)->orderBy('bulan')->get();

        if ($periodes->isEmpty()) {
            PeriodeService::siapkanPerTahun($tahun);
            $periodes = PeriodeAkuntansi::where('tahun', $tahun)->orderBy('bulan')->get();
        }

        $tahunList = range(now()->year - 2, now()->year + 1);

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('akuntansi.periode.index', compact('periodes', 'tahun', 'tahunList', 'kolomOptions', 'kolomAktif'));
    }

    public function buka(Request $request, PeriodeAkuntansi $periode)
    {
        try {
            PeriodeService::buka($periode);

            return redirect()->route('periode.index', ['tahun' => $periode->tahun])->with('success', "Periode {$periode->label} berhasil dibuka.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function kunci(Request $request, PeriodeAkuntansi $periode)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            PeriodeService::kunci($periode, $validated['reason'] ?? null);

            return redirect()->route('periode.index', ['tahun' => $periode->tahun])->with('success', "Periode {$periode->label} berhasil dikunci.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function bukaKunci(PeriodeAkuntansi $periode)
    {
        try {
            PeriodeService::bukaKunci($periode);

            return redirect()->route('periode.index', ['tahun' => $periode->tahun])->with('success', "Kunci periode {$periode->label} dibuka.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
