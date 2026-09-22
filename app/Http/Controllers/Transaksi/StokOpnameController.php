<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\StokOpname;
use App\Services\JournalService;
use App\Services\NomorGenerator;
use App\Services\PengaturanSistemService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokOpnameController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'stok_opname_kolom';

    public const KOLOM_OPTIONS = [
        'nomor' => 'Nomor',
        'tanggal' => 'Tanggal',
        'selisih' => 'Selisih Qty',
        'nilai' => 'Nilai',
        'status' => 'Status',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['nomor', 'tanggal', 'selisih', 'nilai', 'status'];

    public const KOLOM_WAJIB = ['aksi'];

    public function index(Request $request)
    {
        $stokOpname = StokOpname::with('items')
            ->when($request->filled('dari'), fn ($q) => $q->where('tanggal', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->where('tanggal', '<=', $request->sampai))
            ->orderByDesc('tanggal')
            ->paginate(15)->withQueryString();

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('inventori.stok-opname.index', compact('stokOpname', 'kolomOptions', 'kolomAktif'));
    }

    public function create()
    {
        $barang = Barang::aktif()->barang()->orderBy('nama')->get();

        return view('inventori.stok-opname.create', compact('barang'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        DB::beginTransaction();
        try {
            $stokOpname = new StokOpname;
            $stokOpname->nomor = NomorGenerator::generate('SO', $data['tanggal']);
            $stokOpname->tanggal = $data['tanggal'];
            $stokOpname->keterangan = $data['keterangan'] ?? null;
            $stokOpname->status = 'posted';
            $stokOpname->created_by = auth()->id();
            $stokOpname->save();

            $ket = 'Stok Opname '.$stokOpname->nomor;
            $jurnalItems = [];

            foreach ($data['items'] as $item) {
                $barang = Barang::findOrFail($item['barang_id']);
                if ($barang->tipe !== 'barang') {
                    throw new \RuntimeException("Barang {$barang->nama} bukan tipe barang.");
                }

                $stokSistem = (float) $barang->stok;
                $stokFisik = (float) $item['stok_fisik'];
                $selisih = round($stokFisik - $stokSistem, 2);
                $ratt = (float) $barang->harga_avg;
                $subtotal = round(abs($selisih) * $ratt, 2);

                if ($selisih > 0) {
                    StockService::masukBarang($barang, $selisih, $ratt, $data['tanggal'], $ket, $stokOpname);
                    if ($subtotal > 0) {
                        $jurnalItems[] = ['akun_id' => $this->akunPersediaan(), 'debit' => $subtotal, 'kredit' => 0];
                        $jurnalItems[] = ['akun_id' => $this->akunPendapatanLain(), 'debit' => 0, 'kredit' => $subtotal];
                    }
                } elseif ($selisih < 0) {
                    StockService::keluarBarang($barang, abs($selisih), $data['tanggal'], $ket, $stokOpname, $ratt);
                    if ($subtotal > 0) {
                        $jurnalItems[] = ['akun_id' => $this->akunBebanLain(), 'debit' => $subtotal, 'kredit' => 0];
                        $jurnalItems[] = ['akun_id' => $this->akunPersediaan(), 'debit' => 0, 'kredit' => $subtotal];
                    }
                }

                $stokOpname->items()->create([
                    'barang_id' => $barang->id,
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                    'harga' => $ratt,
                    'subtotal' => $subtotal,
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }

            if (count($jurnalItems) > 0) {
                JournalService::post('penyesuaian_stok', $data['tanggal'], $jurnalItems, $ket, $stokOpname);
            }

            DB::commit();

            return redirect()->route('stok-opname.show', $stokOpname)->with('success', 'Stok opname berhasil diposting.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(StokOpname $stokOpname)
    {
        $stokOpname->load(['items.barang', 'jurnal.items.akun', 'creator', 'approver']);

        return view('inventori.stok-opname.show', compact('stokOpname'));
    }

    public function void(StokOpname $stokOpname)
    {
        if ($stokOpname->status === 'draft') {
            return back()->with('error', 'Stok opname ini sudah dibatalkan.');
        }

        DB::beginTransaction();
        try {
            foreach ($stokOpname->jurnal as $jurnal) {
                JournalService::void($jurnal, 'Pembatalan stok opname');
            }

            $ket = 'BATAL Stok Opname '.$stokOpname->nomor;
            foreach ($stokOpname->items as $item) {
                if (! $item->barang) {
                    continue;
                }
                $harga = (float) $item->harga > 0 ? (float) $item->harga : (float) $item->barang->harga_avg;
                $selisih = (float) $item->selisih;

                if ($selisih > 0) {
                    StockService::reverseMasuk($item->barang, $selisih, $harga, now()->toDateString(), $ket, $stokOpname);
                } elseif ($selisih < 0) {
                    StockService::masukBarang($item->barang, abs($selisih), $harga, now()->toDateString(), $ket, $stokOpname);
                }
            }

            $stokOpname->update(['status' => 'draft']);
            DB::commit();

            return redirect()->route('stok-opname.index')->with('success', 'Stok opname berhasil dibatalkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.stok_fisik' => 'required|numeric|min:0',
            'items.*.keterangan' => 'nullable|string',
        ]);
    }

    private function akunPersediaan(): int
    {
        return (int) (PengaturanSistemService::akunId('persediaan') ?? 0);
    }

    private function akunBebanLain(): int
    {
        return (int) (PengaturanSistemService::akunId('rugi_lain') ?? 0);
    }

    private function akunPendapatanLain(): int
    {
        return (int) (PengaturanSistemService::akunId('pendapatan_lain') ?? 0);
    }
}
