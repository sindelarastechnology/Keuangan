<?php

namespace App\Http\Controllers\Akuntansi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Models\AkunPerkiraan;
use App\Models\JurnalUmum;
use App\Services\JournalService;
use Illuminate\Http\Request;

class JurnalController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'jurnal_kolom';

    public const KOLOM_OPTIONS = [
        'nomor' => 'Nomor',
        'tanggal' => 'Tanggal',
        'keterangan' => 'Keterangan',
        'tipe' => 'Tipe',
        'debit' => 'Debit',
        'kredit' => 'Kredit',
        'status' => 'Status',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['nomor', 'tanggal', 'keterangan', 'tipe', 'debit', 'kredit', 'status'];

    public const KOLOM_WAJIB = ['aksi'];

    public function index(Request $request)
    {
        $query = JurnalUmum::withCount('items')
            ->when($request->filled('dari'), fn ($q) => $q->where('tanggal', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->where('tanggal', '<=', $request->sampai))
            ->when($request->filled('tipe'), fn ($q) => $q->where('tipe', $request->tipe))
            ->orderByDesc('tanggal')->orderByDesc('id');

        $jurnal = $query->paginate(15)->withQueryString();
        $tipes = [
            'kas_masuk' => 'Kas Masuk', 'kas_keluar' => 'Kas Keluar', 'mutasi_bank' => 'Mutasi Bank',
            'pembelian' => 'Pembelian', 'penjualan' => 'Penjualan', 'manual' => 'Jurnal Manual',
            'retur_penjualan' => 'Retur Penjualan', 'retur_pembelian' => 'Retur Pembelian',
            'penyesuaian_stok' => 'Penyesuaian Stok',
            'perolehan_aset' => 'Perolehan Aset', 'penyusutan' => 'Penyusutan',
            'penghapusan_aset' => 'Penghapusan Aset', 'pembayaran' => 'Pembayaran',
            'tutup_buku' => 'Tutup Buku', 'hpp' => 'HPP',
        ];

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('akuntansi.jurnal.index', compact('jurnal', 'tipes', 'kolomOptions', 'kolomAktif'));
    }

    public function show(JurnalUmum $jurnal)
    {
        $jurnal->load(['items.akun']);

        return view('akuntansi.jurnal.show', compact('jurnal'));
    }

    public function create()
    {
        $akunList = AkunPerkiraan::leaf()->orderBy('kode')->get();

        return view('akuntansi.jurnal.create', compact('akunList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:2',
            'items.*.akun_id' => 'required|exists:akun_perkiraan,id',
            'items.*.debit' => 'nullable|numeric|min:0',
            'items.*.kredit' => 'nullable|numeric|min:0',
        ]);

        try {
            $jurnalItems = [];
            foreach ($data['items'] as $item) {
                $jurnalItems[] = [
                    'akun_id' => $item['akun_id'],
                    'debit' => $item['debit'] ?? 0,
                    'kredit' => $item['kredit'] ?? 0,
                    'keterangan' => $item['keterangan'] ?? null,
                ];
            }

            JournalService::post('manual', $data['tanggal'], $jurnalItems, $data['keterangan'] ?? 'Jurnal Manual');

            return redirect()->route('jurnal.index')->with('success', 'Jurnal manual berhasil diposting.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function void(JurnalUmum $jurnal)
    {
        try {
            JournalService::void($jurnal);

            return redirect()->route('jurnal.index')->with('success', 'Jurnal berhasil dibatalkan (reversal dibuat).');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function requestApproval(JurnalUmum $jurnal)
    {
        try {
            $jurnal->requestApproval();

            return back()->with('success', 'Jurnal dikirim untuk persetujuan.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function approve(JurnalUmum $jurnal)
    {
        if (! $jurnal->canApprove(auth()->user())) {
            abort(403, 'Anda tidak memiliki izin untuk menyetujui jurnal.');
        }

        try {
            $jurnal->approve(auth()->user());
            $jurnal->update(['is_posted' => true]);

            return back()->with('success', 'Jurnal berhasil disetujui.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(JurnalUmum $jurnal, Request $request)
    {
        if (! $jurnal->canApprove(auth()->user())) {
            abort(403, 'Anda tidak memiliki izin untuk menyetujui jurnal.');
        }

        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $jurnal->reject(auth()->user(), $data['reason']);
            $jurnal->update(['is_posted' => false]);

            return back()->with('success', 'Jurnal ditolak dan dikeluarkan dari laporan.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
