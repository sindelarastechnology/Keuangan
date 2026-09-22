<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\StokGudang;
use App\Models\TransferGudang;
use App\Services\NomorGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TransferGudangController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'transfer_gudang_kolom';

    public const KOLOM_OPTIONS = [
        'nomor' => 'Nomor',
        'tanggal' => 'Tanggal',
        'asal' => 'Asal',
        'tujuan' => 'Tujuan',
        'qty' => 'Total Qty',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['nomor', 'tanggal', 'asal', 'tujuan', 'qty'];

    public const KOLOM_WAJIB = ['aksi'];

    public function index(Request $request)
    {
        $transfers = TransferGudang::with('items')
            ->when($request->filled('dari'), fn ($q) => $q->where('tanggal', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->where('tanggal', '<=', $request->sampai))
            ->orderByDesc('tanggal')
            ->paginate(15)->withQueryString();

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('inventori.transfer-gudang.index', compact('transfers', 'kolomOptions', 'kolomAktif'));
    }

    public function create()
    {
        $gudangs = Gudang::aktif()->orderBy('nama')->get();
        $barang = Barang::aktif()->barang()->orderBy('nama')->get();

        // Map stok per gudang: gudang_id -> barang_id -> qty
        $stokGudang = [];
        foreach (StokGudang::whereIn('gudang_id', $gudangs->pluck('id'))->get() as $sg) {
            $stokGudang[$sg->gudang_id][$sg->barang_id] = (float) $sg->qty;
        }

        // Galeri untuk tampilan kartu per produk
        $galeri = $barang->map(fn ($b) => [
            'id' => $b->id,
            'label' => $b->label,
            'kode' => $b->kode,
            'tipe' => $b->tipe,
            'stok' => (float) $b->stok,
            'foto' => $b->foto_url,
            'inisial' => $b->inisial,
            'warna' => $b->avatarWarna,
        ])->values();

        return view('inventori.transfer-gudang.create', compact('gudangs', 'barang', 'stokGudang', 'galeri'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $asal = Gudang::findOrFail($data['gudang_asal']);
        $tujuan = Gudang::findOrFail($data['gudang_tujuan']);

        if ($asal->id === $tujuan->id) {
            return back()->with('error', 'Gudang asal dan tujuan tidak boleh sama.')->withInput();
        }

        DB::beginTransaction();
        try {
            $transfer = new TransferGudang;
            $transfer->nomor = NomorGenerator::generate('TG', $data['tanggal']);
            $transfer->tanggal = $data['tanggal'];
            $transfer->gudang_asal = $asal->id;
            $transfer->gudang_tujuan = $tujuan->id;
            $transfer->keterangan = $data['keterangan'] ?? null;
            $transfer->status = 'posted';
            $transfer->created_by = auth()->id();
            $transfer->save();

            foreach ($data['items'] as $item) {
                $barang = Barang::findOrFail($item['barang_id']);
                if ($barang->tipe !== 'barang') {
                    throw new RuntimeException("Barang {$barang->nama} bukan tipe barang.");
                }

                $qty = (float) $item['jumlah'];

                // Pastikan baris stok gudang sudah ada (terutama gudang utama barang).
                $barang->seimbangkanStokGudang();

                $stokAsal = (float) StokGudang::where('barang_id', $barang->id)
                    ->where('gudang_id', $asal->id)
                    ->value('qty');

                if ($stokAsal < $qty) {
                    throw new RuntimeException("Stok {$barang->nama} di {$asal->nama} tidak mencukupi (tersedia ".$this->formatQty($stokAsal).').');
                }

                $barisAsal = StokGudang::where('barang_id', $barang->id)
                    ->where('gudang_id', $asal->id)
                    ->first();
                $barisTujuan = StokGudang::firstOrCreate(
                    ['barang_id' => $barang->id, 'gudang_id' => $tujuan->id],
                    ['qty' => 0]
                );

                $barisAsal->decrement('qty', $qty);
                $barisTujuan->increment('qty', $qty);

                $transfer->items()->create([
                    'barang_id' => $barang->id,
                    'jumlah' => $qty,
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('transfer-gudang.show', $transfer)
                ->with('success', 'Transfer barang antar gudang berhasil dicatat.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(TransferGudang $transferGudang)
    {
        $transferGudang->load(['items.barang', 'asal', 'tujuan', 'creator']);

        return view('inventori.transfer-gudang.show', compact('transferGudang'));
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'tanggal' => 'required|date',
            'gudang_asal' => 'required|exists:gudangs,id',
            'gudang_tujuan' => 'required|exists:gudangs,id',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.jumlah' => 'required|numeric|min:0.01',
            'items.*.keterangan' => 'nullable|string',
        ]);
    }

    private function formatQty(float $qty): string
    {
        return rtrim(rtrim(number_format($qty, 2, ',', '.'), '0'), ',');
    }
}
