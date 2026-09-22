<?php

namespace App\Http\Controllers\Akuntansi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Models\Aset;
use App\Services\DepresiasiService;
use Illuminate\Http\Request;
use RuntimeException;

class PenyusutanController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'penyusutan_kolom';

    public const KOLOM_OPTIONS = [
        'kode' => 'Kode',
        'nama' => 'Nama Aset',
        'harga' => 'Harga Perolehan',
        'akumulasi' => 'Akumulasi',
        'nilai_buku' => 'Nilai Buku',
        'beban' => 'Beban Periode',
        'status' => 'Status',
    ];

    public const KOLOM_DEFAULT = ['kode', 'nama', 'harga', 'akumulasi', 'nilai_buku', 'beban', 'status'];

    public const KOLOM_WAJIB = [];

    public function index(Request $request)
    {
        $periode = $request->filled('periode') ? $request->periode : now()->format('Y-m');

        $asets = Aset::withSum('penyusutan', 'beban')
            ->with(['penyusutan' => fn ($q) => $q->where('periode', $periode), 'akunAset'])
            ->orderBy('nama')
            ->get();

        $ringkasan = [
            'total_aset' => $asets->where('status', 'aktif')->count(),
            'nilai_perolehan' => $asets->where('status', 'aktif')->sum(fn ($a) => (float) $a->harga_perolehan),
            'total_akumulasi' => $asets->sum(fn ($a) => round($a->akumulasi, 2)),
            'total_nilai_buku' => $asets->sum(fn ($a) => round($a->nilai_buku, 2)),
            'beban_periode' => $asets->sum(fn ($a) => round($a->penyusutan->sum('beban'), 2)),
            'sudah_diproses' => $asets->contains(fn ($a) => $a->penyusutan->isNotEmpty()),
        ];

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('akuntansi.penyusutan.index', compact('periode', 'asets', 'ringkasan', 'kolomOptions', 'kolomAktif'));
    }

    public function proses(Request $request)
    {
        $periode = $request->validate(['periode' => 'required|date_format:Y-m'])['periode'];

        try {
            $jurnal = DepresiasiService::proses($periode);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        if (! $jurnal) {
            return back()->with('info', 'Tidak ada aset yang perlu disusutkan untuk periode '.$periode.'.');
        }

        return back()->with('success', 'Penyusutan periode '.$periode.' berhasil diproses (jurnal '.$jurnal->nomor.').');
    }

    public function batalkan(Request $request)
    {
        $periode = $request->validate(['periode' => 'required|date_format:Y-m'])['periode'];

        try {
            DepresiasiService::batalkan($periode);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return back()->with('success', 'Penyusutan periode '.$periode.' berhasil dibatalkan.');
    }

    public function kalkulator()
    {
        return view('akuntansi.penyusutan.kalkulator');
    }
}
