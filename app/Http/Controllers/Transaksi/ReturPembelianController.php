<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BbHutang;
use App\Models\Pembelian;
use App\Models\PembelianItem;
use App\Models\Rekening;
use App\Models\ReturPembelian;
use App\Services\JournalService;
use App\Services\NomorGenerator;
use App\Services\PengaturanSistemService;
use App\Services\StockService;
use App\Services\StokGudangService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturPembelianController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'retur_pembelian_kolom';

    public const KOLOM_OPTIONS = [
        'nomor' => 'Nomor',
        'tanggal' => 'Tanggal',
        'pembelian' => 'Pembelian',
        'supplier' => 'Supplier',
        'jumlah' => 'Jumlah',
        'nilai' => 'Nilai',
        'status' => 'Status',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['nomor', 'tanggal', 'pembelian', 'supplier', 'jumlah', 'nilai', 'status'];

    public const KOLOM_WAJIB = ['aksi'];

    public function index(Request $request)
    {
        $returPembelian = ReturPembelian::with('pembelian.supplier', 'items')
            ->when($request->filled('dari'), fn ($q) => $q->where('tanggal', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->where('tanggal', '<=', $request->sampai))
            ->orderByDesc('tanggal')
            ->paginate(15)->withQueryString();

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('inventori.retur-pembelian.index', compact('returPembelian', 'kolomOptions', 'kolomAktif'));
    }

    public function create()
    {
        return view('inventori.retur-pembelian.create', [
            'daftarPembelian' => $this->daftarReturable(limit: PHP_INT_MAX, tebalkan: PHP_INT_MAX),
        ]);
    }

    public function search(Request $request)
    {
        return response()->json($this->daftarReturable(q: trim((string) $request->query('q', ''))));
    }

    /**
     * Daftar pembelian yang masih bisa diretur (status posted, berisi barang,
     * dan masih ada sisa yang belum diretur).
     */
    private function daftarReturable(string $q = '', int $limit = 40, int $tebalkan = 20): array
    {
        return Pembelian::with(['supplier', 'items.barang'])
            ->where('status', 'posted')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nomor', 'like', "%{$q}%")
                        ->orWhereHas('supplier', fn ($s) => $s->where('nama', 'like', "%{$q}%"));
                });
            })
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('pembelian_items')
                    ->join('barang', 'barang.id', '=', 'pembelian_items.barang_id')
                    ->whereColumn('pembelian_items.pembelian_id', 'pembelians.id')
                    ->where('barang.tipe', 'barang')
                    ->whereRaw(
                        '(pembelian_items.jumlah - COALESCE(('
                        .'SELECT SUM(rpi.jumlah) FROM retur_pembelian_items rpi'
                        .' JOIN retur_pembelian rp ON rp.id = rpi.retur_pembelian_id'
                        .' WHERE rpi.pembelian_item_id = pembelian_items.id AND rp.status = ? AND rp.user_id = ?'
                        .'), 0)) > 0',
                        ['posted', auth()->id()]
                    );
            })
            ->orderByDesc('tanggal')
            ->limit($limit)
            ->get()
            ->take($tebalkan)
            ->map(function (Pembelian $pembelian) {
                return [
                    'id' => $pembelian->id,
                    'nomor' => $pembelian->nomor,
                    'nama' => $pembelian->supplier?->nama ?? 'Tanpa Supplier',
                    'tanggal' => (string) $pembelian->tanggal,
                    'total' => (float) $pembelian->total,
                    'label' => "{$pembelian->nomor} — ".($pembelian->supplier?->nama ?? 'Tanpa Supplier').' ('.formatTanggalSingkat($pembelian->tanggal).')',
                ];
            })
            ->values()
            ->toArray();
    }

    public function sumberItems(Pembelian $pembelian)
    {
        $items = $pembelian->items()
            ->with('barang')
            ->get()
            ->filter(fn (PembelianItem $item) => $item->barang && $item->barang->tipe === 'barang')
            ->map(function (PembelianItem $item) {
                $sisa = (float) $item->jumlah - $this->sisaQtyRetur($item);
                $hargaNet = (float) ($item->harga_net > 0 ? $item->harga_net : $item->harga_satuan);

                return [
                    'pembelian_item_id' => $item->id,
                    'barang_id' => $item->barang_id,
                    'nama' => $item->barang->nama,
                    'jumlah_original' => (float) $item->jumlah,
                    'harga_satuan' => (float) $item->harga_satuan,
                    'harga' => $hargaNet,
                    'sisa' => round($sisa, 2),
                ];
            })
            ->values()
            ->toArray();

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        DB::beginTransaction();
        try {
            $pembelian = Pembelian::findOrFail($data['pembelian_id']);

            if ($pembelian->status !== 'posted') {
                throw new \RuntimeException('Pembelian yang dipilih sudah dibatalkan, tidak dapat diretur.');
            }

            $subtotalRetur = 0;
            $itemRows = [];

            foreach ($data['items'] as $item) {
                $pembelianItem = PembelianItem::findOrFail($item['pembelian_item_id']);

                if ($pembelianItem->pembelian_id !== (int) $pembelian->id) {
                    throw new \RuntimeException('Item tidak sesuai dengan pembelian yang dipilih.');
                }

                $sisa = (float) $pembelianItem->jumlah - $this->sisaQtyRetur($pembelianItem);
                $qty = (float) $item['jumlah'];
                if ($qty <= 0 || $qty > $sisa) {
                    throw new \RuntimeException("Jumlah retur melebihi sisa yang dapat diretur (sisa: {$sisa}).");
                }

                $harga = (float) ($pembelianItem->harga_net > 0 ? $pembelianItem->harga_net : $pembelianItem->harga_satuan);
                $sub = $qty * $harga;

                $subtotalRetur += $sub;

                $itemRows[] = [
                    'pembelian_item_id' => $pembelianItem->id,
                    'barang_id' => $pembelianItem->barang_id,
                    'jumlah' => $qty,
                    'harga_satuan' => $harga,
                    'subtotal' => $sub,
                ];
            }

            $retur = new ReturPembelian;
            $retur->nomor = NomorGenerator::generate('RPB', $data['tanggal']);
            $retur->tanggal = $data['tanggal'];
            $retur->pembelian_id = $pembelian->id;
            $retur->keterangan = $data['keterangan'] ?? null;
            $retur->status = 'posted';
            $retur->created_by = auth()->id();
            $retur->save();

            foreach ($itemRows as $row) {
                $retur->items()->create($row);
            }

            // 1. Reverse stok: kurangi stok dengan reverseMasuk (seperti void pembelian)
            $gudangId = (int) ($pembelian->gudang_id ?? PengaturanSistemService::gudangPembelian() ?? 0);
            foreach ($itemRows as $row) {
                $brg = Barang::findOrFail($row['barang_id']);
                StockService::reverseMasuk($brg, (float) $row['jumlah'], (float) $row['harga_satuan'], $data['tanggal'], 'Retur Pembelian '.$retur->nomor, $retur);
                if ($gudangId > 0) {
                    StokGudangService::kurangi($brg, $gudangId, (float) $row['jumlah']);
                }
            }

            // 2. Jurnal retur: kurangi utang / kas (termasuk PPN Masukan proporsional)
            $akunPersediaan = $this->akunPersediaan();
            $dasarPajak = (float) $pembelian->subtotal - (float) $pembelian->diskon_nominal;
            $pajakPortion = $dasarPajak > 0
                ? round((float) $pembelian->pajak_nominal * ($subtotalRetur / $dasarPajak), 2)
                : 0;
            $akunPpnMasukan = PengaturanSistemService::akunId('ppn_masukan');
            $totalRetur = $subtotalRetur + $pajakPortion;

            $jurnalItems = [['akun_id' => $akunPersediaan, 'debit' => 0, 'kredit' => $subtotalRetur]];
            if ($pajakPortion > 0 && $akunPpnMasukan) {
                $jurnalItems[] = ['akun_id' => $akunPpnMasukan, 'debit' => 0, 'kredit' => $pajakPortion];
            }

            if ($pembelian->metode_bayar === 'kredit') {
                $akunUtang = PengaturanSistemService::akunId('utang');
                $jurnalItems[] = ['akun_id' => $akunUtang, 'debit' => $totalRetur, 'kredit' => 0];
            } else {
                $akunRekening = optional(Rekening::find($pembelian->rekening_id))?->akun_id;
                if ($akunRekening) {
                    $jurnalItems[] = ['akun_id' => $akunRekening, 'debit' => $totalRetur, 'kredit' => 0];
                }
            }

            JournalService::post('retur_pembelian', $data['tanggal'], $jurnalItems, 'Retur Pembelian '.$retur->nomor.' - '.$pembelian->supplier->nama, $retur);

            // 3. Catat BB hutang jika kredit
            if ($pembelian->metode_bayar === 'kredit') {
                $this->catatBbHutang($pembelian, $retur, $data['tanggal'], 'Retur Pembelian '.$retur->nomor, $totalRetur, 0);
            }

            DB::commit();

            return redirect()->route('retur-pembelian.show', $retur)->with('success', 'Retur pembelian berhasil dicatat.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(ReturPembelian $returPembelian)
    {
        $returPembelian->load(['pembelian', 'pembelian.supplier', 'items.barang', 'jurnal.items.akun', 'creator', 'approver']);

        return view('inventori.retur-pembelian.show', compact('returPembelian'));
    }

    public function void(ReturPembelian $returPembelian)
    {
        if ($returPembelian->status === 'draft') {
            return back()->with('error', 'Retur ini sudah dibatalkan.');
        }

        $pembelian = $returPembelian->pembelian;
        $metodeBayar = $pembelian?->metode_bayar;

        DB::beginTransaction();
        try {
            foreach ($returPembelian->jurnal as $jurnal) {
                JournalService::void($jurnal, 'Pembatalan retur pembelian');
            }

            // Reverse BB hutang jika kredit
            if ($metodeBayar === 'kredit' && $pembelian) {
                $subtotalRetur = (float) $returPembelian->items->sum('subtotal');
                $dasarPajak = (float) $pembelian->subtotal - (float) $pembelian->diskon_nominal;
                $pajakPortion = $dasarPajak > 0
                    ? round((float) $pembelian->pajak_nominal * ($subtotalRetur / $dasarPajak), 2)
                    : 0;
                $this->catatBbHutang($pembelian, $returPembelian, now()->toDateString(), 'BATAL Retur Pembelian '.$returPembelian->nomor, 0, $subtotalRetur + $pajakPortion);
            }

            // Reverse stok: tambah kembali stok yang dikurangi retur
            foreach ($returPembelian->items as $item) {
                if (! $item->barang) {
                    continue;
                }
                StockService::masukBarang($item->barang, (float) $item->jumlah, (float) $item->harga_satuan, now()->toDateString(), 'BATAL Retur Pembelian '.$returPembelian->nomor, $returPembelian);
            }

            $returPembelian->update(['status' => 'draft']);
            DB::commit();

            return redirect()->route('retur-pembelian.index')->with('success', 'Retur pembelian berhasil dibatalkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'tanggal' => 'required|date',
            'pembelian_id' => 'required|exists:pembelians,id',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.pembelian_item_id' => 'required|exists:pembelian_items,id',
            'items.*.jumlah' => 'required|numeric|min:0.01',
        ]);
    }

    private function sisaQtyRetur(PembelianItem $pembelianItem): float
    {
        $returned = DB::table('retur_pembelian_items')
            ->join('retur_pembelian', 'retur_pembelian.id', '=', 'retur_pembelian_items.retur_pembelian_id')
            ->where('retur_pembelian_items.user_id', auth()->id())
            ->where('retur_pembelian_items.pembelian_item_id', $pembelianItem->id)
            ->where('retur_pembelian.status', 'posted')
            ->sum('retur_pembelian_items.jumlah');

        return (float) $returned;
    }

    private function catatBbHutang(Pembelian $pembelian, $retur, string $tanggal, string $ket, float $debit, float $kredit): void
    {
        $last = BbHutang::where('supplier_id', $pembelian->supplier_id)->orderByDesc('id')->value('saldo');
        BbHutang::create([
            'supplier_id' => $pembelian->supplier_id,
            'pembelian_id' => $pembelian->id,
            'jurnal_id' => null,
            'tanggal' => $tanggal,
            'keterangan' => $ket,
            'debit' => $debit,
            'kredit' => $kredit,
            'saldo' => (float) ($last ?? 0) + $kredit - $debit,
        ]);
    }

    private function akunPersediaan(): int
    {
        return (int) (PengaturanSistemService::akunId('persediaan') ?? 0);
    }
}
