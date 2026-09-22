<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BbPiutang;
use App\Models\Penjualan;
use App\Models\PenjualanItem;
use App\Models\Rekening;
use App\Models\ReturPenjualan;
use App\Services\JournalService;
use App\Services\NomorGenerator;
use App\Services\PengaturanSistemService;
use App\Services\StockService;
use App\Services\StokGudangService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturPenjualanController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'retur_penjualan_kolom';

    public const KOLOM_OPTIONS = [
        'nomor' => 'Nomor',
        'tanggal' => 'Tanggal',
        'penjualan' => 'Penjualan',
        'pelanggan' => 'Pelanggan',
        'jumlah' => 'Jumlah',
        'nilai' => 'Nilai',
        'status' => 'Status',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['nomor', 'tanggal', 'penjualan', 'pelanggan', 'jumlah', 'nilai', 'status'];

    public const KOLOM_WAJIB = ['aksi'];

    public function index(Request $request)
    {
        $returPenjualan = ReturPenjualan::with('penjualan.customer', 'items')
            ->when($request->filled('dari'), fn ($q) => $q->where('tanggal', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->where('tanggal', '<=', $request->sampai))
            ->orderByDesc('tanggal')
            ->paginate(15)->withQueryString();

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('inventori.retur-penjualan.index', compact('returPenjualan', 'kolomOptions', 'kolomAktif'));
    }

    public function create()
    {
        return view('inventori.retur-penjualan.create', [
            'daftarPenjualan' => $this->daftarReturable(limit: PHP_INT_MAX, tebalkan: PHP_INT_MAX),
        ]);
    }

    public function search(Request $request)
    {
        return response()->json($this->daftarReturable(q: trim((string) $request->query('q', ''))));
    }

    /**
     * Daftar penjualan yang masih bisa diretur (status posted, berisi barang,
     * dan masih ada sisa yang belum diretur).
     */
    private function daftarReturable(string $q = '', int $limit = 40, int $tebalkan = 20): array
    {
        return Penjualan::with(['customer', 'items.barang'])
            ->where('status', 'posted')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nomor', 'like', "%{$q}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('nama', 'like', "%{$q}%"));
                });
            })
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('penjualan_items')
                    ->join('barang', 'barang.id', '=', 'penjualan_items.barang_id')
                    ->whereColumn('penjualan_items.penjualan_id', 'penjualans.id')
                    ->where('barang.tipe', 'barang')
                    ->whereRaw(
                        '(penjualan_items.jumlah - COALESCE(('
                        .'SELECT SUM(rpi.jumlah) FROM retur_penjualan_items rpi'
                        .' JOIN retur_penjualan rp ON rp.id = rpi.retur_penjualan_id'
                        .' WHERE rpi.penjualan_item_id = penjualan_items.id AND rp.status = ? AND rp.user_id = ?'
                        .'), 0)) > 0',
                        ['posted', auth()->id()]
                    );
            })
            ->orderByDesc('tanggal')
            ->limit($limit)
            ->get()
            ->take($tebalkan)
            ->map(function (Penjualan $penjualan) {
                return [
                    'id' => $penjualan->id,
                    'nomor' => $penjualan->nomor,
                    'nama' => $penjualan->customer?->nama ?? 'Tanpa Pelanggan',
                    'tanggal' => (string) $penjualan->tanggal,
                    'total' => (float) $penjualan->total,
                    'label' => "{$penjualan->nomor} — ".($penjualan->customer?->nama ?? 'Tanpa Pelanggan').' ('.formatTanggalSingkat($penjualan->tanggal).')',
                ];
            })
            ->values()
            ->toArray();
    }

    public function sumberItems(Penjualan $penjualan)
    {
        $faktor = $this->faktorNilaiBersih($penjualan);

        $items = $penjualan->items()
            ->with('barang')
            ->get()
            ->filter(fn (PenjualanItem $item) => $item->barang && $item->barang->tipe === 'barang')
            ->map(function (PenjualanItem $item) use ($faktor) {
                $sisa = (float) $item->jumlah - $this->sisaQtyRetur($item);

                return [
                    'penjualan_item_id' => $item->id,
                    'barang_id' => $item->barang_id,
                    'nama' => $item->barang->nama,
                    'jumlah_original' => (float) $item->jumlah,
                    'harga_satuan' => (float) $item->harga_satuan,
                    'harga' => $this->hargaNetUnit($item, $faktor),
                    'hpp' => (float) $item->hpp,
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
            $penjualan = Penjualan::findOrFail($data['penjualan_id']);

            if ($penjualan->status !== 'posted') {
                throw new \RuntimeException('Penjualan yang dipilih sudah dibatalkan, tidak dapat diretur.');
            }

            $faktor = $this->faktorNilaiBersih($penjualan);

            $subtotalRetur = 0;
            $hppTotal = 0;
            $itemRows = [];

            foreach ($data['items'] as $item) {
                $penjualanItem = PenjualanItem::findOrFail($item['penjualan_item_id']);

                if ($penjualanItem->penjualan_id !== (int) $penjualan->id) {
                    throw new \RuntimeException('Item tidak sesuai dengan penjualan yang dipilih.');
                }

                $sisa = (float) $penjualanItem->jumlah - $this->sisaQtyRetur($penjualanItem);
                $qty = (float) $item['jumlah'];
                if ($qty <= 0 || $qty > $sisa) {
                    throw new \RuntimeException("Jumlah retur melebihi sisa yang dapat diretur (sisa: {$sisa}).");
                }

                $harga = $this->hargaNetUnit($penjualanItem, $faktor);
                $hpp = (float) $penjualanItem->hpp;
                $sub = round($qty * $harga, 2);
                $hppSub = $qty * $hpp;

                $subtotalRetur += $sub;
                $hppTotal += $hppSub;

                $itemRows[] = [
                    'penjualan_item_id' => $penjualanItem->id,
                    'barang_id' => $penjualanItem->barang_id,
                    'jumlah' => $qty,
                    'harga_satuan' => $harga,
                    'hpp' => $hpp,
                    'subtotal' => $sub,
                    'hpp_total' => $hppSub,
                ];
            }

            $retur = new ReturPenjualan;
            $retur->nomor = NomorGenerator::generate('RPJ', $data['tanggal']);
            $retur->tanggal = $data['tanggal'];
            $retur->penjualan_id = $penjualan->id;
            $retur->keterangan = $data['keterangan'] ?? null;
            $retur->status = 'posted';
            $retur->created_by = auth()->id();
            $retur->save();

            foreach ($itemRows as $row) {
                $retur->items()->create($row);
            }

            // 1. Kembalikan stok (seperti void penjualan)
            $itemsReady = collect($itemRows)->keyBy('barang_id');
            $gudangId = (int) ($penjualan->gudang_id ?? PengaturanSistemService::gudangPenjualan() ?? 0);
            foreach ($itemsReady as $row) {
                $brg = Barang::findOrFail($row['barang_id']);
                StockService::masukBarang($brg, (float) $row['jumlah'], (float) $row['hpp'], $data['tanggal'], 'Retur Penjualan '.$retur->nomor, $retur);
                if ($gudangId > 0) {
                    StokGudangService::tambah($brg, $gudangId, (float) $row['jumlah']);
                }
            }

            // 2. Jurnal retur: kurangi pendapatan / piutang (termasuk PPN Keluaran proporsional)
            $akunPenjualan = PengaturanSistemService::akunId('penjualan');
            $dasarPajak = (float) $penjualan->subtotal - (float) $penjualan->diskon_nominal;
            $pajakPortion = $dasarPajak > 0
                ? round((float) $penjualan->pajak_nominal * ($subtotalRetur / $dasarPajak), 2)
                : 0;
            $akunPpnKeluaran = PengaturanSistemService::akunId('ppn_keluaran');
            $totalRetur = $subtotalRetur + $pajakPortion;

            $jurnalItems = [['akun_id' => $akunPenjualan, 'debit' => $subtotalRetur, 'kredit' => 0]];
            if ($pajakPortion > 0 && $akunPpnKeluaran) {
                $jurnalItems[] = ['akun_id' => $akunPpnKeluaran, 'debit' => $pajakPortion, 'kredit' => 0];
            }

            if ($penjualan->metode_bayar === 'kredit') {
                $akunPiutang = PengaturanSistemService::akunId('piutang');
                $jurnalItems[] = ['akun_id' => $akunPiutang, 'debit' => 0, 'kredit' => $totalRetur];
            } else {
                $akunRekening = optional(Rekening::find($penjualan->rekening_id))?->akun_id;
                if ($akunRekening) {
                    $jurnalItems[] = ['akun_id' => $akunRekening, 'debit' => 0, 'kredit' => $totalRetur];
                }
            }

            $rekeningTunai = $penjualan->metode_bayar !== 'kredit'
                ? Rekening::find((int) ($penjualan->rekening_id ?? 0))
                : null;
            $saldoKurang = $rekeningTunai && (float) $rekeningTunai->saldo < (float) $totalRetur ? $rekeningTunai : null;

            JournalService::post('retur_penjualan', $data['tanggal'], $jurnalItems, 'Retur Penjualan '.$retur->nomor.' - '.$penjualan->customer->nama, $retur);

            // 3. Jurnal HPP reversal
            if ($hppTotal > 0) {
                $akunHpp = PengaturanSistemService::akunId('hpp');
                $akunPersediaan = $this->akunPersediaan();
                JournalService::post('hpp', $data['tanggal'], [
                    ['akun_id' => $akunPersediaan, 'debit' => $hppTotal, 'kredit' => 0],
                    ['akun_id' => $akunHpp, 'debit' => 0, 'kredit' => $hppTotal],
                ], 'HPP Retur Penjualan '.$retur->nomor, $retur);
            }

            // 4. Catat BB piutang jika kredit
            if ($penjualan->metode_bayar === 'kredit') {
                $this->catatBbPiutang($penjualan, $retur, $data['tanggal'], 'Retur Penjualan '.$retur->nomor, 0, $totalRetur);
            }

            DB::commit();

            $redirect = redirect()->route('retur-penjualan.show', $retur)->with('success', 'Retur penjualan berhasil dicatat.');

            if ($saldoKurang) {
                return $redirect->with('warning', 'Saldo rekening tidak mencukupi ('.$saldoKurang->nama.': '.formatRupiah($saldoKurang->saldo).'). Pengembalian tetap dicatat, namun saldo rekening menjadi minus.');
            }

            return $redirect;
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(ReturPenjualan $returPenjualan)
    {
        $returPenjualan->load(['penjualan', 'penjualan.customer', 'items.barang', 'jurnal.items.akun', 'creator', 'approver']);

        return view('inventori.retur-penjualan.show', compact('returPenjualan'));
    }

    public function void(ReturPenjualan $returPenjualan)
    {
        if ($returPenjualan->status === 'draft') {
            return back()->with('error', 'Retur ini sudah dibatalkan.');
        }

        $penjualan = $returPenjualan->penjualan;
        $metodeBayar = $penjualan?->metode_bayar;

        DB::beginTransaction();
        try {
            foreach ($returPenjualan->jurnal as $jurnal) {
                JournalService::void($jurnal, 'Pembatalan retur penjualan');
            }

            // Reverse BB piutang jika kredit
            if ($metodeBayar === 'kredit' && $penjualan) {
                $subtotalRetur = (float) $returPenjualan->items->sum('subtotal');
                $dasarPajak = (float) $penjualan->subtotal - (float) $penjualan->diskon_nominal;
                $pajakPortion = $dasarPajak > 0
                    ? round((float) $penjualan->pajak_nominal * ($subtotalRetur / $dasarPajak), 2)
                    : 0;
                $this->catatBbPiutang($penjualan, $returPenjualan, now()->toDateString(), 'BATAL Retur Penjualan '.$returPenjualan->nomor, $subtotalRetur + $pajakPortion, 0);
            }

            // Reverse stok: kurangi stok yang ditambah retur
            foreach ($returPenjualan->items as $item) {
                if (! $item->barang) {
                    continue;
                }
                $harga = (float) $item->hpp > 0 ? (float) $item->hpp : (float) $item->barang->harga_avg;
                StockService::reverseMasuk($item->barang, (float) $item->jumlah, $harga, now()->toDateString(), 'BATAL Retur Penjualan '.$returPenjualan->nomor, $returPenjualan);
            }

            $returPenjualan->update(['status' => 'draft']);
            DB::commit();

            return redirect()->route('retur-penjualan.index')->with('success', 'Retur penjualan berhasil dibatalkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'tanggal' => 'required|date',
            'penjualan_id' => 'required|exists:penjualans,id',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.penjualan_item_id' => 'required|exists:penjualan_items,id',
            'items.*.jumlah' => 'required|numeric|min:0.01',
        ]);
    }

    private function sisaQtyRetur(PenjualanItem $penjualanItem): float
    {
        $returned = DB::table('retur_penjualan_items')
            ->join('retur_penjualan', 'retur_penjualan.id', '=', 'retur_penjualan_items.retur_penjualan_id')
            ->where('retur_penjualan_items.user_id', auth()->id())
            ->where('retur_penjualan_items.penjualan_item_id', $penjualanItem->id)
            ->where('retur_penjualan.status', 'posted')
            ->sum('retur_penjualan_items.jumlah');

        return (float) $returned;
    }

    private function catatBbPiutang(Penjualan $penjualan, $retur, string $tanggal, string $ket, float $debit, float $kredit): void
    {
        $last = BbPiutang::where('customer_id', $penjualan->customer_id)->orderByDesc('id')->value('saldo');
        BbPiutang::create([
            'customer_id' => $penjualan->customer_id,
            'penjualan_id' => $penjualan->id,
            'jurnal_id' => null,
            'tanggal' => $tanggal,
            'keterangan' => $ket,
            'debit' => $debit,
            'kredit' => $kredit,
            'saldo' => (float) ($last ?? 0) + $debit - $kredit,
        ]);
    }

    private function akunPersediaan(): int
    {
        return (int) (PengaturanSistemService::akunId('persediaan') ?? 0);
    }

    private function faktorNilaiBersih(Penjualan $penjualan): float
    {
        $subtotal = (float) $penjualan->subtotal;

        return $subtotal > 0
            ? round(max(0, $subtotal - (float) $penjualan->diskon_nominal) / $subtotal, 6)
            : 1.0;
    }

    private function hargaNetUnit(PenjualanItem $item, float $faktor): float
    {
        $netPerUnit = $item->jumlah > 0 ? $item->subtotal / $item->jumlah : (float) $item->harga_satuan;

        return round($netPerUnit * $faktor, 2);
    }
}
