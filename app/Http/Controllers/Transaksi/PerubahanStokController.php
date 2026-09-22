<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\PerubahanStok;
use App\Services\JournalService;
use App\Services\NomorGenerator;
use App\Services\PengaturanSistemService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerubahanStokController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'perubahan_stok_kolom';

    public const KOLOM_OPTIONS = [
        'nomor' => 'Nomor',
        'tanggal' => 'Tanggal',
        'jenis' => 'Jenis',
        'nilai' => 'Nilai',
        'status' => 'Status',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['nomor', 'tanggal', 'jenis', 'nilai', 'status'];

    public const KOLOM_WAJIB = ['aksi'];

    public function index(Request $request)
    {
        $perubahanStok = PerubahanStok::with('items')
            ->when($request->filled('dari'), fn ($q) => $q->where('tanggal', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->where('tanggal', '<=', $request->sampai))
            ->orderByDesc('tanggal')
            ->paginate(15)->withQueryString();

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('inventori.perubahan-stok.index', compact('perubahanStok', 'kolomOptions', 'kolomAktif'));
    }

    public function create()
    {
        $barang = Barang::aktif()->barang()->orderBy('nama')->get();

        return view('inventori.perubahan-stok.create', compact('barang'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        DB::beginTransaction();
        try {
            $perubahanStok = new PerubahanStok;
            $perubahanStok->nomor = NomorGenerator::generate('PS', $data['tanggal']);
            $perubahanStok->tanggal = $data['tanggal'];
            $perubahanStok->jenis = $data['jenis'];
            $perubahanStok->keterangan = $data['keterangan'] ?? null;
            $perubahanStok->status = 'posted';
            $perubahanStok->created_by = auth()->id();
            $perubahanStok->save();

            $ket = 'Perubahan Stok '.$perubahanStok->nomor.' ('.ucfirst($data['jenis']).')';
            $jurnalItems = [];

            foreach ($data['items'] as $item) {
                $barang = Barang::findOrFail($item['barang_id']);
                if ($barang->tipe !== 'barang') {
                    throw new \RuntimeException("Barang {$barang->nama} bukan tipe barang.");
                }

                $qty = (float) $item['jumlah'];
                $ratt = (float) $barang->harga_avg;
                $arah = $item['arah'];

                if ($arah === 'masuk') {
                    [$stokBaru, $rattBaru, $delta] = StockService::masukBarang(
                        $barang, $qty, $ratt, $data['tanggal'], $ket, $perubahanStok
                    );
                    $subtotal = $delta;
                    if ($subtotal > 0) {
                        $jurnalItems[] = ['akun_id' => $this->akunPersediaan(), 'debit' => $subtotal, 'kredit' => 0];
                        $jurnalItems[] = ['akun_id' => $this->akunPendapatanLain(), 'debit' => 0, 'kredit' => $subtotal];
                    }
                } else {
                    [, , , $hppTotal] = StockService::keluarBarang(
                        $barang, $qty, $data['tanggal'], $ket, $perubahanStok, $ratt
                    );
                    $subtotal = $hppTotal;
                    if ($subtotal > 0) {
                        $jurnalItems[] = ['akun_id' => $this->akunBebanLain(), 'debit' => $subtotal, 'kredit' => 0];
                        $jurnalItems[] = ['akun_id' => $this->akunPersediaan(), 'debit' => 0, 'kredit' => $subtotal];
                    }
                }

                $perubahanStok->items()->create([
                    'barang_id' => $barang->id,
                    'arah' => $arah,
                    'jumlah' => $qty,
                    'harga' => $ratt,
                    'subtotal' => $subtotal,
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }

            if (count($jurnalItems) > 0) {
                JournalService::post('penyesuaian_stok', $data['tanggal'], $jurnalItems, $ket, $perubahanStok);
            }

            DB::commit();

            return redirect()->route('perubahan-stok.show', $perubahanStok)->with('success', 'Perubahan stok berhasil dicatat.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(PerubahanStok $perubahanStok)
    {
        $perubahanStok->load(['items.barang', 'jurnal.items.akun', 'creator', 'approver']);

        return view('inventori.perubahan-stok.show', compact('perubahanStok'));
    }

    public function void(PerubahanStok $perubahanStok)
    {
        if ($perubahanStok->status === 'draft') {
            return back()->with('error', 'Perubahan stok ini sudah dibatalkan.');
        }

        DB::beginTransaction();
        try {
            foreach ($perubahanStok->jurnal as $jurnal) {
                JournalService::void($jurnal, 'Pembatalan perubahan stok');
            }

            $ket = 'BATAL Perubahan Stok '.$perubahanStok->nomor;
            foreach ($perubahanStok->items as $item) {
                if (! $item->barang) {
                    continue;
                }
                $harga = (float) $item->harga > 0 ? (float) $item->harga : (float) $item->barang->harga_avg;

                if ($item->arah === 'masuk') {
                    StockService::reverseMasuk($item->barang, (float) $item->jumlah, $harga, now()->toDateString(), $ket, $perubahanStok);
                } else {
                    StockService::masukBarang($item->barang, (float) $item->jumlah, $harga, now()->toDateString(), $ket, $perubahanStok);
                }
            }

            $perubahanStok->update(['status' => 'draft']);
            DB::commit();

            return redirect()->route('perubahan-stok.index')->with('success', 'Perubahan stok berhasil dibatalkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:rusak,hilang,salah,lebih,lainnya',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.arah' => 'required|in:masuk,keluar',
            'items.*.jumlah' => 'required|numeric|min:0.01',
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
