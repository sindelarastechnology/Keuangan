<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\StorePembelianRequest;
use App\Models\Barang;
use App\Models\BbHutang;
use App\Models\DaftarHarga;
use App\Models\Gudang;
use App\Models\Pajak;
use App\Models\Pembelian;
use App\Models\PembelianItem;
use App\Models\Rekening;
use App\Models\Supplier;
use App\Services\JournalService;
use App\Services\NomorGenerator;
use App\Services\PelunasanService;
use App\Services\PengaturanSistemService;
use App\Services\StockService;
use App\Services\StokGudangService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembelianController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'pembelian_kolom';

    public const KOLOM_OPTIONS = [
        'nomor' => 'Nomor',
        'tanggal' => 'Tanggal',
        'supplier' => 'Supplier',
        'bayar' => 'Bayar',
        'total' => 'Total',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['nomor', 'tanggal', 'supplier', 'bayar', 'total'];

    public const KOLOM_WAJIB = ['aksi'];

    public function index(Request $request)
    {
        $pembelian = Pembelian::with(['supplier', 'retur' => fn ($q) => $q->where('status', 'posted')])
            ->when($request->filled('dari'), fn ($q) => $q->where('tanggal', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->where('tanggal', '<=', $request->sampai))
            ->orderByDesc('tanggal')
            ->paginate(15)->withQueryString();

        $sisaPeta = BbHutang::whereIn('pembelian_id', $pembelian->pluck('id'))
            ->whereNotNull('pembelian_id')
            ->selectRaw('pembelian_id, SUM(kredit - debit) as total')
            ->groupBy('pembelian_id')
            ->pluck('total', 'pembelian_id');

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('transaksi.pembelian.index', compact('pembelian', 'sisaPeta', 'kolomOptions', 'kolomAktif'));
    }

    public function create()
    {
        return view('transaksi.pembelian.create', $this->formDataPembelian());
    }

    public function edit(Pembelian $pembelian)
    {
        if ($pembelian->status !== 'pending') {
            return redirect()->route('pembelian.show', $pembelian)->with('error', 'Hanya transaksi draft yang dapat diedit.');
        }

        $pembelian->load('items');

        return view('transaksi.pembelian.create', array_merge(
            $this->formDataPembelian(),
            ['pembelian' => $pembelian, 'editData' => $this->editDataPembelian($pembelian)]
        ));
    }

    public function update(StorePembelianRequest $request, Pembelian $pembelian)
    {
        if ($pembelian->status !== 'pending') {
            return back()->with('error', 'Hanya transaksi draft yang dapat diubah.');
        }

        $data = $request->validated();
        $aksi = $data['aksi'] ?? 'pending';
        $data['sync_harga'] = filter_var($data['sync_harga'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $computed = $this->hitungTransaksiPembelian($data);
        if ($computed instanceof RedirectResponse) {
            return $computed;
        }

        $saldoKurang = null;
        if ($data['metode_bayar'] === 'tunai') {
            $saldoKurang = $this->saldoRekeningKurang((int) ($data['rekening_id'] ?? 0), (float) $computed['total']);
        }

        DB::beginTransaction();
        try {
            if ($aksi === 'pending') {
                $this->simpanHeaderPembelian($data, $computed, 'pending', $pembelian, $data['sync_harga']);
                DB::commit();

                return redirect()->route('pembelian.show', $pembelian)->with('success', 'Draft pembelian berhasil diperbarui.');
            }

            $supplier = Supplier::findOrFail($data['supplier_id']);
            $this->simpanHeaderPembelian($data, $computed, 'posted', $pembelian, $data['sync_harga']);
            $this->prosesPostingPembelian($pembelian, $data, $computed, $supplier);
            DB::commit();

            $redirect = redirect()->route('pembelian.show', $pembelian)->with('success', 'Pembelian berhasil diposting.');

            return $this->tambahkanPeringatanSaldo($redirect, $saldoKurang);
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Data bersama untuk halaman create & edit pembelian.
     *
     * @return array<string, mixed>
     */
    private function formDataPembelian(): array
    {
        $suppliers = Supplier::aktif()->orderBy('nama')->get();
        $barang = Barang::aktif()->barang()->orderBy('nama')->get();
        $rekeningList = Rekening::aktif()->orderBy('nama')->get();
        $pajakList = Pajak::aktif()->get();

        // Map harga: supplier_id -> barang_id -> [{min_qty, max_qty, harga}, ...] (tiered)
        $hargaMap = [];
        foreach (DaftarHarga::aktif()->supplier()->get() as $dh) {
            $hargaMap[$dh->supplier_id][$dh->barang_id][] = [
                'min_qty' => (float) $dh->min_qty,
                'max_qty' => $dh->max_qty !== null ? (float) $dh->max_qty : null,
                'harga' => (float) $dh->harga,
            ];
        }
        // Sort tiers by min_qty ascending for each supplier+barang
        foreach ($hargaMap as $supId => &$barangs) {
            foreach ($barangs as $barangId => &$tiers) {
                usort($tiers, fn ($a, $b) => $a['min_qty'] <=> $b['min_qty']);
            }
        }

        // Fallback harga dari barang.harga_beli
        $barangHargaMap = $barang->pluck('harga_beli', 'id')->map(fn ($h) => (float) $h)->toArray();

        // Opsi sumber harga per supplier+barang (auto / daftar harga / terakhir / riwayat / standar)
        $priceOpts = $this->buildPriceOptsBeli($hargaMap, $barangHargaMap, $this->hargaTerakhirBeli());

        // Galeri item untuk tampilan POS kasir
        $galeri = $barang->map(fn ($b) => [
            'id' => $b->id,
            'label' => $b->label,
            'kode' => $b->kode,
            'tipe' => $b->tipe,
            'stok' => (float) $b->stok,
            'harga' => (float) $b->harga_beli,
            'foto' => $b->foto_url,
            'inisial' => $b->inisial,
            'warna' => $b->avatarWarna,
        ])->values();

        // Supplier default: UMUM (bisa diubah user)
        $supplierDefaultId = Supplier::umum()?->id;

        // Gudang tujuan pembelian: default dari pengaturan sistem
        $gudangList = Gudang::aktif()->orderBy('nama')->get();
        $gudangPembelianId = PengaturanSistemService::gudangPembelian();

        return compact('suppliers', 'barang', 'rekeningList', 'pajakList', 'hargaMap', 'barangHargaMap', 'priceOpts', 'galeri', 'supplierDefaultId', 'gudangList', 'gudangPembelianId');
    }

    public function store(StorePembelianRequest $request)
    {
        $data = $request->validated();
        $aksi = $data['aksi'] ?? 'posted';
        $data['sync_harga'] = filter_var($data['sync_harga'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $computed = $this->hitungTransaksiPembelian($data);
        if ($computed instanceof RedirectResponse) {
            return $computed;
        }

        $saldoKurang = null;
        if ($aksi === 'posted' && ($data['metode_bayar'] ?? 'tunai') === 'tunai') {
            $saldoKurang = $this->saldoRekeningKurang((int) ($data['rekening_id'] ?? 0), (float) $computed['total']);
        }

        DB::beginTransaction();
        try {
            if ($aksi === 'pending') {
                $pembelian = $this->simpanHeaderPembelian($data, $computed, 'pending', null, $data['sync_harga']);
                DB::commit();

                return redirect()->route('pembelian.show', $pembelian)->with('success', 'Pembelian disimpan sebagai draft. Posting draft untuk mengunci data transaksi.');
            }

            $supplier = Supplier::findOrFail($data['supplier_id']);
            $pembelian = $this->simpanHeaderPembelian($data, $computed, 'posted', null, $data['sync_harga']);
            $this->prosesPostingPembelian($pembelian, $data, $computed, $supplier);
            DB::commit();

            $redirect = redirect()->route('pembelian.show', $pembelian)->with('success', 'Pembelian berhasil dicatat.');

            return $this->tambahkanPeringatanSaldo($redirect, $saldoKurang);
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Posting draft pembelian (status pending) menjadi transaksi final ter-akui.
     */
    public function post(Pembelian $pembelian)
    {
        if ($pembelian->status !== 'pending') {
            return back()->with('error', 'Hanya transaksi draft yang dapat diposting.');
        }

        $data = $this->dataDariHeaderPembelian($pembelian);
        $data['sync_harga'] = (bool) $pembelian->sync_harga;

        $computed = $this->hitungTransaksiPembelian($data);
        if ($computed instanceof RedirectResponse) {
            return $computed;
        }

        $saldoKurang = null;
        if (($data['metode_bayar'] ?? 'tunai') === 'tunai') {
            $saldoKurang = $this->saldoRekeningKurang((int) ($data['rekening_id'] ?? 0), (float) $computed['total']);
        }

        DB::beginTransaction();
        try {
            $supplier = Supplier::findOrFail($pembelian->supplier_id);
            $this->simpanHeaderPembelian($data, $computed, 'posted', $pembelian, (bool) $pembelian->sync_harga);
            $this->prosesPostingPembelian($pembelian, $data, $computed, $supplier);
            DB::commit();

            $redirect = redirect()->route('pembelian.show', $pembelian)->with('success', 'Pembelian berhasil diposting.');

            return $this->tambahkanPeringatanSaldo($redirect, $saldoKurang);
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Hitung nilai transaksi pembelian dari input yang sudah divalidasi.
     */
    private function hitungTransaksiPembelian(array $data): array|RedirectResponse
    {
        $subtotal = 0;
        $grossTotal = 0;
        $rawItems = [];

        foreach ($data['items'] as $item) {
            $qty = (float) $item['jumlah'];
            $harga = (float) $item['harga_satuan'];
            $diskonItem = (float) ($item['diskon'] ?? 0);
            $gross = $qty * $harga;
            if ($diskonItem > $gross) {
                return back()->withErrors(['items' => 'Diskon item tidak boleh melebihi nilai itemnya.'])->withInput();
            }
            $subItem = $gross - $diskonItem;
            $subtotal += $subItem;
            $grossTotal += $gross;
            $rawItems[] = $item + [
                'qty' => $qty,
                'harga' => $harga,
                'gross' => $gross,
                'net' => $subItem,
                'diskon_item' => $diskonItem,
                'subtotal_item' => $subItem,
            ];
        }

        if ($data['diskon_tipe'] === 'persen' && (float) ($data['diskon'] ?? 0) > 100) {
            return back()->withErrors(['diskon' => 'Diskon persen tidak boleh lebih dari 100%.'])->withInput();
        }
        if ($data['diskon_tipe'] === 'nominal' && (float) ($data['diskon'] ?? 0) > $subtotal) {
            return back()->withErrors(['diskon' => 'Diskon tidak boleh melebihi subtotal.'])->withInput();
        }

        $diskonNominal = $this->hitungDiskon($data, $subtotal);
        $dasarPajak = $subtotal - $diskonNominal;
        $pajak = isset($data['pajak_id']) ? Pajak::find($data['pajak_id']) : null;
        $pajakNominal = $pajak ? $dasarPajak * ($pajak->rate / 100) : 0;
        $ongkir = (float) ($data['ongkir'] ?? 0);
        $total = $dasarPajak + $pajakNominal + $ongkir;

        // Alokasikan diskon global proporsional ke tiap baris -> harga netto efektif
        $itemRows = [];
        foreach ($rawItems as $item) {
            $share = $diskonNominal > 0 && $grossTotal > 0
                ? $diskonNominal * ($item['gross'] / $grossTotal)
                : 0;
            $netHarga = round(($item['net'] - $share) / $item['qty'], 2);
            $netHarga = max(0, $netHarga);
            $itemRows[] = [
                'barang_id' => $item['barang_id'],
                'jumlah' => $item['qty'],
                'harga_satuan' => $item['harga'],
                'harga_net' => $netHarga,
                'diskon' => $item['diskon_item'],
                'subtotal' => $item['subtotal_item'],
            ];
        }

        return compact('subtotal', 'grossTotal', 'itemRows', 'diskonNominal', 'dasarPajak', 'pajak', 'pajakNominal', 'ongkir', 'total');
    }

    private function saldoRekeningKurang(?int $rekeningId, float $total): ?Rekening
    {
        $rekening = $rekeningId ? Rekening::find($rekeningId) : null;

        return $rekening && (float) $rekening->saldo < $total ? $rekening : null;
    }

    private function tambahkanPeringatanSaldo(RedirectResponse $redirect, ?Rekening $rekening): RedirectResponse
    {
        if (! $rekening) {
            return $redirect;
        }

        return $redirect->with('warning', 'Saldo rekening tidak mencukupi ('.$rekening->nama.': '.formatRupiah($rekening->saldo).'). Transaksi tetap dicatat, namun saldo rekening menjadi minus.');
    }

    private function simpanHeaderPembelian(array $data, array $computed, string $status, ?Pembelian $existing = null, bool $syncHarga = false): Pembelian
    {
        $pembelian = $existing ?? new Pembelian;
        if (! $existing) {
            $pembelian->nomor = NomorGenerator::generate('PB', $data['tanggal']);
            $pembelian->created_by = auth()->id();
        }
        $pembelian->tanggal = $data['tanggal'];
        $pembelian->supplier_id = $data['supplier_id'];
        $pembelian->gudang_id = $data['gudang_id'] ?? PengaturanSistemService::gudangPembelian();
        $pembelian->metode_bayar = $data['metode_bayar'];
        $pembelian->rekening_id = $data['metode_bayar'] === 'kredit' ? null : ($data['rekening_id'] ?? null);
        $pembelian->subtotal = $computed['subtotal'];
        $pembelian->diskon = $data['diskon'] ?? 0;
        $pembelian->diskon_tipe = $data['diskon_tipe'] ?? 'nominal';
        $pembelian->diskon_nominal = $computed['diskonNominal'];
        $pembelian->pajak_id = $data['pajak_id'] ?? null;
        $pembelian->pajak_nominal = $computed['pajakNominal'];
        $pembelian->ongkir = $computed['ongkir'];
        $pembelian->total = $computed['total'];
        $pembelian->status = $status;
        $pembelian->keterangan = $data['keterangan'] ?? null;
        $pembelian->sync_harga = $syncHarga;
        $pembelian->save();

        if ($existing) {
            $pembelian->items()->delete();
        }
        foreach ($computed['itemRows'] as $row) {
            $pembelian->items()->create($row);
        }

        return $pembelian;
    }

    private function prosesPostingPembelian(Pembelian $pembelian, array $data, array $computed, Supplier $supplier): void
    {
        // 1. Update stok + BB persediaan (masuk) dengan harga netto (sudah dipotong diskon)
        $gudangId = (int) ($data['gudang_id'] ?? PengaturanSistemService::gudangPembelian() ?? 0);
        foreach ($computed['itemRows'] as $row) {
            $brg = Barang::findOrFail($row['barang_id']);
            StockService::masukBarang(
                $brg, (float) $row['jumlah'], (float) $row['harga_net'], $data['tanggal'], 'Pembelian '.$pembelian->nomor, $pembelian
            );
            if ($gudangId > 0) {
                StokGudangService::tambah($brg, $gudangId, (float) $row['jumlah']);
            }
        }

        // 1b. Sinkron daftar harga (opsional): harga aktual transaksi menjadi harga acuan baru
        if ($data['sync_harga']) {
            foreach ($computed['itemRows'] as $row) {
                try {
                    $brg = Barang::findOrFail($row['barang_id']);
                    if ($brg->tipe === 'barang') {
                        DaftarHarga::sinkronBeli($supplier->id, $brg->id, (float) $row['jumlah'], (float) $row['harga_satuan']);
                    }
                } catch (\Throwable $e) {
                    Log::warning('Sinkron harga beli gagal', ['pembelian' => $pembelian->nomor, 'barang_id' => $row['barang_id'], 'error' => $e->getMessage()]);
                }
            }
        }

        // 2. Posting jurnal
        $akunPersediaan = $this->akunPersediaan();
        $akunUtang = $this->akunUtangDagang();
        $jurnalItems = [
            ['akun_id' => $akunPersediaan, 'debit' => $computed['dasarPajak'], 'kredit' => 0],
        ];

        if ($computed['pajak']) {
            $akunPpnMasukan = $this->akunPpnMasukan();
            $jurnalItems[] = ['akun_id' => $akunPpnMasukan, 'debit' => $computed['pajakNominal'], 'kredit' => 0];
        }

        if ($computed['ongkir'] > 0) {
            $jurnalItems[] = ['akun_id' => $this->akunBebanTransport(), 'debit' => $computed['ongkir'], 'kredit' => 0];
        }

        if ($data['metode_bayar'] === 'kredit') {
            $jurnalItems[] = ['akun_id' => $akunUtang, 'debit' => 0, 'kredit' => $computed['total']];
        } else {
            $akunRekening = Rekening::findOrFail($data['rekening_id'])->akun_id;
            $jurnalItems[] = ['akun_id' => $akunRekening, 'debit' => 0, 'kredit' => $computed['total']];
        }

        $jurnal = JournalService::post('pembelian', $data['tanggal'], $jurnalItems, 'Pembelian '.$pembelian->nomor.' - '.$supplier->nama, $pembelian);

        // 3. Catat BB hutang untuk kredit
        if ($data['metode_bayar'] === 'kredit') {
            $this->catatBbHutang($supplier, $jurnal, $data['tanggal'], 'Pembelian '.$pembelian->nomor, 0, $computed['total'], $pembelian);
        }
    }

    private function dataDariHeaderPembelian(Pembelian $pembelian): array
    {
        return [
            'tanggal' => $pembelian->tanggal->toDateString(),
            'supplier_id' => $pembelian->supplier_id,
            'gudang_id' => $pembelian->gudang_id,
            'metode_bayar' => $pembelian->metode_bayar,
            'rekening_id' => $pembelian->rekening_id,
            'diskon' => (float) $pembelian->diskon,
            'diskon_tipe' => $pembelian->diskon_tipe,
            'pajak_id' => $pembelian->pajak_id,
            'ongkir' => (float) $pembelian->ongkir,
            'keterangan' => $pembelian->keterangan,
            'items' => $pembelian->items->map(fn ($i) => [
                'barang_id' => $i->barang_id,
                'jumlah' => (float) $i->jumlah,
                'harga_satuan' => (float) $i->harga_satuan,
                'diskon' => (float) $i->diskon,
            ])->all(),
        ];
    }

    /**
     * Data draft pembelian (status pending) untuk pra-isi form edit.
     *
     * @return array<string, mixed>
     */
    private function editDataPembelian(Pembelian $pembelian): array
    {
        $data = $this->dataDariHeaderPembelian($pembelian);
        $data['sync_harga'] = (bool) $pembelian->sync_harga;
        $data['items'] = collect($data['items'])->map(fn ($i) => $i + ['sumber' => 'standar'])->all();

        return $data;
    }

    public function show(Pembelian $pembelian)
    {
        $pembelian->load(['supplier', 'rekening', 'pajak', 'gudang', 'items.barang', 'jurnal.items.akun']);
        $rekeningList = Rekening::aktif()->orderBy('nama')->get();

        return view('transaksi.pembelian.show', compact('pembelian', 'rekeningList'));
    }

    public function pdf(Pembelian $pembelian)
    {
        $pembelian->load(['supplier', 'rekening', 'pajak', 'items.barang']);
        $namaPerusahaan = setting('nama_perusahaan', 'Perusahaan');
        $alamatPerusahaan = setting('alamat_perusahaan', '');

        $pdf = Pdf::loadView('transaksi.pembelian.invoice', compact('pembelian', 'namaPerusahaan', 'alamatPerusahaan'));

        return $pdf->download('faktur-pembelian-'.str_replace('/', '-', $pembelian->nomor).'.pdf');
    }

    /**
     * Catat pelunasan hutang pembelian kredit sebagai Kas Keluar
     * (1 item akun utang dagang) yang di-alokasikan ke faktur ini.
     */
    public function pelunasan(Pembelian $pembelian, Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'rekening_id' => 'required|exists:rekenings,id',
            'nominal' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($pembelian->status !== 'posted') {
            return back()->with('error', 'Hanya transaksi yang sudah diposting yang dapat menerima pelunasan.');
        }
        if ($pembelian->metode_bayar !== 'kredit') {
            return back()->with('error', 'Transaksi tunai tidak memiliki hutang yang dapat dilunasi.');
        }

        $sisa = (float) $pembelian->sisaHutang;
        $nominal = (float) $data['nominal'];
        if ($sisa <= 0.005) {
            return back()->with('error', 'Hutang transaksi ini sudah lunas.');
        }
        if ($nominal > $sisa + 0.005) {
            return back()->withErrors(['nominal' => 'Nominal pelunasan melebihi sisa hutang ('.formatRupiah($sisa).').'])->withInput();
        }

        DB::beginTransaction();
        try {
            $rekening = Rekening::findOrFail($data['rekening_id']);
            $supplier = $pembelian->supplier;

            PelunasanService::bayarHutang(
                $supplier,
                $nominal,
                $rekening->id,
                $data['tanggal'],
                $data['keterangan'] ?? 'Pelunasan '.$pembelian->nomor
            );

            DB::commit();

            $redirect = redirect()->route('pembelian.show', $pembelian)->with('success', 'Pelunasan hutang berhasil dicatat.');

            if ((float) $rekening->saldo < $nominal) {
                return $redirect->with('warning', 'Saldo rekening tidak mencukupi ('.$rekening->nama.': '.formatRupiah($rekening->saldo).'). Pelunasan tetap dicatat, namun saldo rekening menjadi minus.');
            }

            return $redirect;
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function void(Pembelian $pembelian, Request $request)
    {
        if ($pembelian->status === 'pending') {
            $pembelian->update(['status' => 'draft']);

            return redirect()->route('pembelian.index')->with('success', 'Draft pembelian dibatalkan.');
        }

        if ($pembelian->retur()->where('status', 'posted')->exists()) {
            return back()->with('error', 'Pembelian tidak dapat dibatalkan karena masih memiliki retur yang aktif. Batalkan retur terlebih dahulu.');
        }

        DB::beginTransaction();
        try {
            $jurnal = $pembelian->jurnal;
            if ($jurnal) {
                JournalService::void($jurnal, 'Pembatalan pembelian');
            }

            // Reverse BB hutang: reversal per-baris untuk SEMUA baris yang
            // terhubung ke faktur ini (baris hutang awal + alokasi pembayaran),
            // dengan saldo berjalan yang saling meniadakan efek baris aslinya.
            if ($pembelian->metode_bayar === 'kredit') {
                $rows = BbHutang::where('pembelian_id', $pembelian->id)
                    ->where('supplier_id', $pembelian->supplier_id)
                    ->orderBy('id')
                    ->get();

                if ($rows->isNotEmpty()) {
                    $lastSaldo = $this->saldoBbHutangTerakhir($pembelian->supplier_id);

                    foreach ($rows as $row) {
                        $lastSaldo += (float) $row->debit - (float) $row->kredit;

                        BbHutang::create([
                            'supplier_id' => $pembelian->supplier_id,
                            'pembelian_id' => $row->pembelian_id,
                            'jurnal_id' => null,
                            'tanggal' => now()->toDateString(),
                            'keterangan' => 'BATAL: Pembelian '.$pembelian->nomor,
                            'debit' => (float) $row->kredit,
                            'kredit' => (float) $row->debit,
                            'saldo' => round($lastSaldo, 2),
                        ]);
                    }
                }
            }

            // Reverse persediaan: kembalikan stok + rata-rata harga seperti sebelum masuk
            $gudangId = (int) ($pembelian->gudang_id ?? PengaturanSistemService::gudangPembelian() ?? 0);
            foreach ($pembelian->items as $item) {
                $brg = $item->barang;
                $hargaReverse = (float) ($item->harga_net > 0 ? $item->harga_net : $item->harga_satuan);
                StockService::reverseMasuk($brg, $item->jumlah, $hargaReverse, now()->toDateString(), 'BATAL Pembelian '.$pembelian->nomor, $pembelian);
                if ($gudangId > 0) {
                    StokGudangService::kurangi($brg, $gudangId, (float) $item->jumlah);
                }
            }

            $pembelian->update(['status' => 'draft']);
            DB::commit();

            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dibatalkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    private function hitungDiskon(array $data, float $subtotal): float
    {
        $diskon = (float) ($data['diskon'] ?? 0);
        if ($data['diskon_tipe'] === 'persen') {
            return $subtotal * ($diskon / 100);
        }

        return min($diskon, $subtotal);
    }

    private function akunPersediaan(): int
    {
        return (int) (PengaturanSistemService::akunId('persediaan') ?? 0);
    }

    private function akunUtangDagang(): int
    {
        return (int) (PengaturanSistemService::akunId('utang') ?? 0);
    }

    private function akunPpnMasukan(): int
    {
        return (int) (PengaturanSistemService::akunId('ppn_masukan') ?? 0);
    }

    private function akunBebanTransport(): int
    {
        $id = PengaturanSistemService::akunId('beban_transport');

        if (! $id) {
            throw new \RuntimeException('Akun Beban Transportasi (524) tidak ditemukan di chart of accounts.');
        }

        return $id;
    }

    private function catatBbHutang(Supplier $supplier, $jurnal, string $tanggal, string $ket, float $debit, float $kredit, ?Pembelian $pembelian = null): void
    {
        $saldoTerakhir = $this->saldoBbHutangTerakhir($supplier->id);
        BbHutang::create([
            'supplier_id' => $supplier->id,
            'pembelian_id' => $pembelian?->id,
            'jurnal_id' => $jurnal->id,
            'tanggal' => $tanggal,
            'keterangan' => $ket,
            'debit' => $debit,
            'kredit' => $kredit,
            'saldo' => $saldoTerakhir + $kredit - $debit,
        ]);
    }

    private function saldoBbHutangTerakhir(int $supplierId): float
    {
        $last = BbHutang::where('supplier_id', $supplierId)->orderByDesc('id')->value('saldo');

        return (float) ($last ?? 0);
    }

    private function labelTier($min, $max): string
    {
        $m = number_format((float) $min, 0, ',', '.');
        if ($max === null) {
            return $m.'+ pcs';
        }
        $x = number_format((float) $max, 0, ',', '.');

        return abs((float) $min - (float) $max) < 0.005 ? $m.' pcs' : $m.'–'.$x.' pcs';
    }

    private function hargaTerakhirBeli(): array
    {
        $rows = PembelianItem::query()
            ->select(
                'pembelian_items.barang_id',
                'pembelian_items.harga_satuan',
                'pembelians.supplier_id',
                'pembelians.nomor',
                'pembelians.tanggal'
            )
            ->join('pembelians', 'pembelians.id', '=', 'pembelian_items.pembelian_id')
            ->where('pembelians.status', 'posted')
            ->orderByDesc('pembelians.tanggal')
            ->orderByDesc('pembelians.id')
            ->get();

        $map = [];
        foreach ($rows as $r) {
            if (! isset($map[$r->supplier_id][$r->barang_id])) {
                $map[$r->supplier_id][$r->barang_id] = [
                    'harga' => (float) $r->harga_satuan,
                    'ref' => $r->nomor,
                    'tanggal' => $r->tanggal,
                ];
            }
        }

        return $map;
    }

    private function histDaftarHargaBeli(): array
    {
        return DaftarHarga::query()
            ->where('entitas', 'supplier')
            ->where('is_aktif', false)
            ->whereNotNull('tanggal_selesai')
            ->orderByDesc('tanggal_selesai')
            ->get()
            ->groupBy(fn ($d) => $d->supplier_id.':'.$d->barang_id)
            ->map(fn ($g) => $g->take(5)->map(fn ($d) => [
                'harga' => (float) $d->harga,
                'tanggal' => $d->tanggal_selesai ? $d->tanggal_selesai->format('d/m/Y') : null,
            ]))
            ->toArray();
    }

    private function buildPriceOptsBeli(array $hargaMap, array $barangHargaMap, array $lastBeliMap): array
    {
        $hist = $this->histDaftarHargaBeli();

        $keys = [];
        foreach ($hargaMap as $supId => $barangs) {
            foreach (array_keys($barangs ?? []) as $barangId) {
                $keys[$supId][$barangId] = true;
            }
        }
        foreach ($lastBeliMap as $supId => $items) {
            foreach (array_keys($items ?? []) as $barangId) {
                $keys[$supId][$barangId] = true;
            }
        }
        foreach ($hist as $key => $_) {
            [$supId, $barangId] = array_pad(explode(':', (string) $key), 2, null);
            if ($supId !== null && $barangId !== null) {
                $keys[(int) $supId][(int) $barangId] = true;
            }
        }

        $opts = [];
        foreach ($keys as $supId => $barangs) {
            foreach (array_keys($barangs) as $barangId) {
                $tiers = (is_array($hargaMap[$supId] ?? null) && is_array($hargaMap[$supId][$barangId] ?? null))
                    ? $hargaMap[$supId][$barangId]
                    : [];
                $base = isset($tiers[0]) ? (float) $tiers[0]['harga'] : null;
                $list = [['k' => 'auto', 'label' => 'Otomatis (Daftar Harga)', 'harga' => null]];

                foreach ($tiers as $i => $t) {
                    $list[] = [
                        'k' => 'dh_'.$i,
                        'label' => 'Daftar Harga · '.$this->labelTier($t['min_qty'], $t['max_qty']).' · '.formatRupiah($t['harga']),
                        'harga' => $t['harga'],
                    ];
                }

                $last = $lastBeliMap[$supId][$barangId] ?? null;
                if ($last) {
                    $label = 'Harga terakhir · '.formatRupiah($last['harga']);
                    if ($base !== null && abs((float) $last['harga'] - (float) $base) > 0.005) {
                        $label .= ' (sebelumnya '.formatRupiah($base).')';
                    }
                    if (! empty($last['ref'])) {
                        $label .= ' · '.$last['ref'];
                    }
                    $list[] = ['k' => 'last', 'label' => $label, 'harga' => $last['harga']];
                }

                foreach (($hist[$supId.':'.$barangId] ?? []) as $j => $h) {
                    $list[] = [
                        'k' => 'hist_'.$j,
                        'label' => 'Daftar Harga lama · '.formatRupiah($h['harga']).($h['tanggal'] ? ' · '.$h['tanggal'] : ''),
                        'harga' => $h['harga'],
                    ];
                }

                $standar = $barangHargaMap[$barangId] ?? null;
                if ($standar !== null) {
                    $list[] = [
                        'k' => 'standar',
                        'label' => 'Standar barang · '.formatRupiah($standar),
                        'harga' => $standar,
                    ];
                }

                $opts[$supId][$barangId] = $list;
            }
        }

        return $opts;
    }
}
