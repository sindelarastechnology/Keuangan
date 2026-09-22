<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\StorePenjualanRequest;
use App\Models\Barang;
use App\Models\BbPiutang;
use App\Models\Customer;
use App\Models\DaftarHarga;
use App\Models\Gudang;
use App\Models\Pajak;
use App\Models\Penjualan;
use App\Models\PenjualanItem;
use App\Models\Rekening;
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

class PenjualanController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'penjualan_kolom';

    public const KOLOM_OPTIONS = [
        'nomor' => 'Nomor',
        'tanggal' => 'Tanggal',
        'customer' => 'Customer',
        'bayar' => 'Bayar',
        'total' => 'Total',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['nomor', 'tanggal', 'customer', 'bayar', 'total'];

    public const KOLOM_WAJIB = ['aksi'];

    public function index(Request $request)
    {
        $penjualan = Penjualan::with(['customer', 'retur' => fn ($q) => $q->where('status', 'posted')])
            ->when($request->filled('dari'), fn ($q) => $q->where('tanggal', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->where('tanggal', '<=', $request->sampai))
            ->orderByDesc('tanggal')
            ->paginate(15)->withQueryString();

        $sisaPeta = BbPiutang::whereIn('penjualan_id', $penjualan->pluck('id'))
            ->whereNotNull('penjualan_id')
            ->selectRaw('penjualan_id, SUM(debit - kredit) as total')
            ->groupBy('penjualan_id')
            ->pluck('total', 'penjualan_id');

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('transaksi.penjualan.index', compact('penjualan', 'sisaPeta', 'kolomOptions', 'kolomAktif'));
    }

    public function create()
    {
        return view('transaksi.penjualan.create', $this->formDataPenjualan());
    }

    public function edit(Penjualan $penjualan)
    {
        if ($penjualan->status !== 'pending') {
            return redirect()->route('penjualan.show', $penjualan)->with('error', 'Hanya transaksi draft yang dapat diedit.');
        }

        $penjualan->load('items');

        return view('transaksi.penjualan.create', array_merge(
            $this->formDataPenjualan(),
            ['penjualan' => $penjualan, 'editData' => $this->editDataPenjualan($penjualan)]
        ));
    }

    public function update(StorePenjualanRequest $request, Penjualan $penjualan)
    {
        if ($penjualan->status !== 'pending') {
            return back()->with('error', 'Hanya transaksi draft yang dapat diubah.');
        }

        $data = $request->validated();
        $aksi = $data['aksi'] ?? 'pending';
        $data['sync_harga'] = filter_var($data['sync_harga'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $computed = $this->hitungTransaksiPenjualan($data, cekStok: $aksi !== 'pending');
        if ($computed instanceof RedirectResponse) {
            return $computed;
        }

        DB::beginTransaction();
        try {
            if ($aksi === 'pending') {
                $this->simpanHeaderPenjualan($data, $computed, 'pending', $penjualan, $data['sync_harga']);
                DB::commit();

                return redirect()->route('penjualan.show', $penjualan)->with('success', 'Draft penjualan berhasil diperbarui.');
            }

            $customer = Customer::findOrFail($data['customer_id']);
            $this->simpanHeaderPenjualan($data, $computed, 'posted', $penjualan, $data['sync_harga']);
            $this->prosesPostingPenjualan($penjualan, $data, $computed, $customer);
            DB::commit();

            return redirect()->route('penjualan.show', $penjualan)->with('success', 'Penjualan berhasil diposting.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Data bersama untuk halaman create & edit penjualan.
     *
     * @return array<string, mixed>
     */
    private function formDataPenjualan(): array
    {
        $customers = Customer::aktif()->orderBy('nama')->get();
        $barang = Barang::aktif()->orderBy('nama')->get();
        $rekeningList = Rekening::aktif()->orderBy('nama')->get();
        $pajakList = Pajak::aktif()->get();

        // Map harga jual tiered per customer: customer_id -> barang_id -> [{min_qty, max_qty, harga}, ...]
        $hargaMap = [];
        foreach (DaftarHarga::aktif()->customer()->get() as $dh) {
            $hargaMap[$dh->customer_id][$dh->barang_id][] = [
                'min_qty' => (float) $dh->min_qty,
                'max_qty' => $dh->max_qty !== null ? (float) $dh->max_qty : null,
                'harga' => (float) $dh->harga,
            ];
        }
        foreach ($hargaMap as $custId => &$barangs) {
            foreach ($barangs as $barangId => &$tiers) {
                usort($tiers, fn ($a, $b) => $a['min_qty'] <=> $b['min_qty']);
            }
        }

        // Fallback harga dari barang.harga_jual
        $barangHargaMap = $barang->pluck('harga_jual', 'id')->map(fn ($h) => (float) $h)->toArray();

        // Opsi sumber harga per customer+barang (auto / daftar harga / terakhir / riwayat / standar)
        $priceOpts = $this->buildPriceOptsJual($hargaMap, $barangHargaMap, $this->hargaTerakhirJual());

        // Galeri item untuk tampilan POS kasir
        $galeri = $barang->map(fn ($b) => [
            'id' => $b->id,
            'label' => $b->label,
            'kode' => $b->kode,
            'tipe' => $b->tipe,
            'stok' => (float) $b->stok,
            'harga' => (float) $b->harga_jual,
            'foto' => $b->foto_url,
            'inisial' => $b->inisial,
            'warna' => $b->avatarWarna,
        ])->values();

        // Customer default: UMUM (bisa diubah user)
        $customerDefaultId = Customer::umum()?->id;

        // Gudang penjualan + peta stok per gudang untuk galeri kasir
        $gudangList = Gudang::aktif()->orderBy('nama')->get();
        $gudangPenjualanId = PengaturanSistemService::gudangPenjualan();
        $stokById = [];
        foreach ($gudangList as $gudang) {
            $stokById[$gudang->id] = StokGudangService::petaStokGudang($barang->pluck('id')->all(), (int) $gudang->id);
        }

        return compact('customers', 'barang', 'rekeningList', 'pajakList', 'hargaMap', 'barangHargaMap', 'priceOpts', 'galeri', 'customerDefaultId', 'gudangList', 'gudangPenjualanId', 'stokById');
    }

    public function store(StorePenjualanRequest $request)
    {
        $data = $request->validated();
        $aksi = $data['aksi'] ?? 'posted';
        $data['sync_harga'] = filter_var($data['sync_harga'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $computed = $this->hitungTransaksiPenjualan($data, cekStok: $aksi !== 'pending');
        if ($computed instanceof RedirectResponse) {
            return $computed;
        }

        DB::beginTransaction();
        try {
            if ($aksi === 'pending') {
                $penjualan = $this->simpanHeaderPenjualan($data, $computed, 'pending', null, $data['sync_harga']);
                DB::commit();

                return redirect()->route('penjualan.show', $penjualan)->with('success', 'Penjualan disimpan sebagai draft. Posting draft untuk mengunci data transaksi.');
            }

            $customer = Customer::findOrFail($data['customer_id']);
            $penjualan = $this->simpanHeaderPenjualan($data, $computed, 'posted', null, $data['sync_harga']);
            $this->prosesPostingPenjualan($penjualan, $data, $computed, $customer);
            DB::commit();

            return redirect()->route('penjualan.show', $penjualan)->with('success', 'Penjualan berhasil dicatat.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Posting draft penjualan (status pending) menjadi transaksi final ter-akui.
     */
    public function post(Penjualan $penjualan)
    {
        if ($penjualan->status !== 'pending') {
            return back()->with('error', 'Hanya transaksi draft yang dapat diposting.');
        }

        $data = $this->dataDariHeaderPenjualan($penjualan);
        $data['sync_harga'] = (bool) $penjualan->sync_harga;

        $computed = $this->hitungTransaksiPenjualan($data, cekStok: true);
        if ($computed instanceof RedirectResponse) {
            return $computed;
        }

        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($penjualan->customer_id);
            $this->simpanHeaderPenjualan($data, $computed, 'posted', $penjualan, (bool) $penjualan->sync_harga);
            $this->prosesPostingPenjualan($penjualan, $data, $computed, $customer);
            DB::commit();

            return redirect()->route('penjualan.show', $penjualan)->with('success', 'Penjualan berhasil diposting.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Hitung nilai transaksi penjualan dari input yang sudah divalidasi.
     */
    private function hitungTransaksiPenjualan(array $data, bool $cekStok): array|RedirectResponse
    {
        $subtotal = 0;
        $subtotalBarang = 0;
        $subtotalJasa = 0;
        $hppTotal = 0;
        $itemRows = [];
        $gudangIdCek = (int) ($data['gudang_id'] ?? PengaturanSistemService::gudangPenjualan() ?? 0);

        foreach ($data['items'] as $item) {
            $barangModel = Barang::findOrFail($item['barang_id']);
            $qty = (float) $item['jumlah'];

            if ($cekStok && $barangModel->tipe === 'barang') {
                $tersedia = $gudangIdCek > 0
                    ? StokGudangService::stokDiGudang($barangModel->id, $gudangIdCek)
                    : (float) $barangModel->stok;
                if ($qty > $tersedia) {
                    return back()->withErrors(['items' => "Stok {$barangModel->nama} di gudang terpilih tidak mencukupi. Tersedia: {$tersedia} {$barangModel->satuan}."])->withInput();
                }
            }

            $diskonItem = (float) ($item['diskon'] ?? 0);
            if ($diskonItem > $qty * (float) $item['harga_satuan']) {
                return back()->withErrors(['items' => "Diskon item {$barangModel->nama} melebihi nilai itemnya."])->withInput();
            }
            $subItem = ($qty * (float) $item['harga_satuan']) - $diskonItem;
            $subtotal += $subItem;

            if ($barangModel->tipe === 'jasa') {
                $subtotalJasa += $subItem;
            } else {
                $subtotalBarang += $subItem;
            }

            $hpp = (float) $barangModel->harga_avg;
            $hppSub = $qty * $hpp;
            $hppTotal += $hppSub;

            $itemRows[] = $item + [
                'jumlah' => $qty,
                'harga_satuan' => $item['harga_satuan'],
                'diskon' => $diskonItem,
                'subtotal' => $subItem,
                'hpp' => $hpp,
                'hpp_total' => $hppSub,
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

        // Alokasikan pendapatan: barang ke 411 (Penjualan Barang), jasa ke 412 (Pendapatan Jasa)
        $pendapatanBarang = (float) $subtotalBarang;
        $pendapatanJasa = (float) $subtotalJasa;

        if ($diskonNominal > 0 && $subtotal > 0) {
            $pendapatanBarang = round($dasarPajak * ($subtotalBarang / $subtotal), 2);
            $pendapatanJasa = round($dasarPajak - $pendapatanBarang, 2);
        }

        return compact('subtotal', 'subtotalBarang', 'subtotalJasa', 'hppTotal', 'itemRows', 'diskonNominal', 'dasarPajak', 'pajak', 'pajakNominal', 'ongkir', 'total', 'pendapatanBarang', 'pendapatanJasa');
    }

    private function simpanHeaderPenjualan(array $data, array $computed, string $status, ?Penjualan $existing = null, bool $syncHarga = false): Penjualan
    {
        $penjualan = $existing ?? new Penjualan;
        if (! $existing) {
            $penjualan->nomor = NomorGenerator::generate('PJ', $data['tanggal']);
            $penjualan->created_by = auth()->id();
        }
        $penjualan->tanggal = $data['tanggal'];
        $penjualan->customer_id = $data['customer_id'];
        $penjualan->gudang_id = $data['gudang_id'] ?? PengaturanSistemService::gudangPenjualan();
        $penjualan->metode_bayar = $data['metode_bayar'];
        $penjualan->rekening_id = $data['metode_bayar'] === 'kredit' ? null : ($data['rekening_id'] ?? null);
        $penjualan->subtotal = $computed['subtotal'];
        $penjualan->diskon = $data['diskon'] ?? 0;
        $penjualan->diskon_tipe = $data['diskon_tipe'] ?? 'nominal';
        $penjualan->diskon_nominal = $computed['diskonNominal'];
        $penjualan->pajak_id = $data['pajak_id'] ?? null;
        $penjualan->pajak_nominal = $computed['pajakNominal'];
        $penjualan->ongkir = $computed['ongkir'];
        $penjualan->total = $computed['total'];
        $penjualan->hpp_total = $computed['hppTotal'];
        $penjualan->status = $status;
        $penjualan->keterangan = $data['keterangan'] ?? null;
        $penjualan->sync_harga = $syncHarga;
        $penjualan->save();

        if ($existing) {
            $penjualan->items()->delete();
        }
        foreach ($computed['itemRows'] as $row) {
            $penjualan->items()->create($row);
        }

        return $penjualan;
    }

    private function prosesPostingPenjualan(Penjualan $penjualan, array $data, array $computed, Customer $customer): void
    {
        // 1. Kurangi stok barang & catat mutasi keluar di BB Persediaan
        $gudangId = (int) ($data['gudang_id'] ?? PengaturanSistemService::gudangPenjualan() ?? 0);
        foreach ($computed['itemRows'] as $row) {
            $barangModel = Barang::findOrFail($row['barang_id']);
            if ($barangModel->tipe === 'barang') {
                StockService::keluarBarang(
                    $barangModel,
                    (float) $row['jumlah'],
                    $data['tanggal'],
                    'Penjualan '.$penjualan->nomor.' - '.$customer->nama,
                    $penjualan,
                    (float) $row['hpp']
                );
                if ($gudangId > 0) {
                    StokGudangService::kurangi($barangModel, $gudangId, (float) $row['jumlah']);
                }
            }
        }

        // 1b. Sinkron daftar harga (opsional)
        if ($data['sync_harga']) {
            foreach ($computed['itemRows'] as $row) {
                try {
                    $barangModel = Barang::findOrFail($row['barang_id']);
                    if ($barangModel->tipe === 'barang') {
                        DaftarHarga::sinkronJual($customer->id, $barangModel->id, (float) $row['jumlah'], (float) $row['harga_satuan']);
                    }
                } catch (\Throwable $e) {
                    Log::warning('Sinkron harga jual gagal', ['penjualan' => $penjualan->nomor, 'barang_id' => $row['barang_id'], 'error' => $e->getMessage()]);
                }
            }
        }

        // 2. Posting jurnal penjualan
        $akunPenjualan = $this->akunPenjualan();
        $akunPpnKeluaran = $this->akunPpnKeluaran();
        $akunPiutang = $this->akunPiutangDagang();

        $jurnalItems = [];

        if ($data['metode_bayar'] === 'kredit') {
            $jurnalItems[] = ['akun_id' => $akunPiutang, 'debit' => $computed['total'], 'kredit' => 0];
        } else {
            $akunRekening = Rekening::findOrFail($data['rekening_id'])->akun_id;
            $jurnalItems[] = ['akun_id' => $akunRekening, 'debit' => $computed['total'], 'kredit' => 0];
        }

        // Kredit pendapatan: barang ke 411, jasa ke 412 (jurnal tetap seimbang)
        $akunJasa = PengaturanSistemService::akunId('penjualan_jasa');

        if ($computed['pendapatanJasa'] > 0 && $akunJasa) {
            if ($computed['pendapatanBarang'] > 0) {
                $jurnalItems[] = ['akun_id' => $akunPenjualan, 'debit' => 0, 'kredit' => $computed['pendapatanBarang']];
            }
            $jurnalItems[] = ['akun_id' => $akunJasa, 'debit' => 0, 'kredit' => $computed['pendapatanJasa']];
        } else {
            $jurnalItems[] = ['akun_id' => $akunPenjualan, 'debit' => 0, 'kredit' => $computed['dasarPajak']];
        }

        if ($computed['pajak']) {
            $jurnalItems[] = ['akun_id' => $akunPpnKeluaran, 'debit' => 0, 'kredit' => $computed['pajakNominal']];
        }

        if ($computed['ongkir'] > 0) {
            $jurnalItems[] = ['akun_id' => $this->akunBebanTransport(), 'debit' => 0, 'kredit' => $computed['ongkir']];
        }

        $jurnal = JournalService::post('penjualan', $data['tanggal'], $jurnalItems, 'Penjualan '.$penjualan->nomor.' - '.$customer->nama, $penjualan);

        // 3. Posting jurnal HPP
        if ($computed['hppTotal'] > 0) {
            $akunHpp = PengaturanSistemService::akunId('hpp');
            $akunPersediaanBarang = PengaturanSistemService::akunId('persediaan') ?? 0;
            JournalService::post('hpp', $data['tanggal'], [
                ['akun_id' => $akunHpp, 'debit' => $computed['hppTotal'], 'kredit' => 0],
                ['akun_id' => $akunPersediaanBarang, 'debit' => 0, 'kredit' => $computed['hppTotal']],
            ], 'HPP Penjualan '.$penjualan->nomor, $penjualan);
        }

        // 4. Catat BB piutang untuk kredit
        if ($data['metode_bayar'] === 'kredit') {
            $this->catatBbPiutang($customer, $jurnal, $data['tanggal'], 'Penjualan '.$penjualan->nomor, $computed['total'], 0, $penjualan);
        }
    }

    private function dataDariHeaderPenjualan(Penjualan $penjualan): array
    {
        return [
            'tanggal' => $penjualan->tanggal->toDateString(),
            'customer_id' => $penjualan->customer_id,
            'gudang_id' => $penjualan->gudang_id,
            'metode_bayar' => $penjualan->metode_bayar,
            'rekening_id' => $penjualan->rekening_id,
            'diskon' => (float) $penjualan->diskon,
            'diskon_tipe' => $penjualan->diskon_tipe,
            'pajak_id' => $penjualan->pajak_id,
            'ongkir' => (float) $penjualan->ongkir,
            'keterangan' => $penjualan->keterangan,
            'items' => $penjualan->items->map(fn ($i) => [
                'barang_id' => $i->barang_id,
                'jumlah' => (float) $i->jumlah,
                'harga_satuan' => (float) $i->harga_satuan,
                'diskon' => (float) $i->diskon,
            ])->all(),
        ];
    }

    /**
     * Data draft penjualan (status pending) untuk pra-isi form edit.
     *
     * @return array<string, mixed>
     */
    private function editDataPenjualan(Penjualan $penjualan): array
    {
        $data = $this->dataDariHeaderPenjualan($penjualan);
        $data['sync_harga'] = (bool) $penjualan->sync_harga;
        $data['items'] = collect($data['items'])->map(fn ($i) => $i + ['sumber' => 'standar'])->all();

        return $data;
    }

    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['customer', 'rekening', 'pajak', 'gudang', 'items.barang', 'jurnal.items.akun']);
        $rekeningList = Rekening::aktif()->orderBy('nama')->get();

        return view('transaksi.penjualan.show', compact('penjualan', 'rekeningList'));
    }

    public function pdf(Penjualan $penjualan)
    {
        $penjualan->load(['customer', 'rekening', 'pajak', 'items.barang']);
        $namaPerusahaan = setting('nama_perusahaan', 'Perusahaan');
        $alamatPerusahaan = setting('alamat_perusahaan', '');

        $pdf = Pdf::loadView('transaksi.penjualan.invoice', compact('penjualan', 'namaPerusahaan', 'alamatPerusahaan'));

        return $pdf->download('invoice-penjualan-'.str_replace('/', '-', $penjualan->nomor).'.pdf');
    }

    /**
     * Catat pelunasan piutang penjualan kredit sebagai Kas Masuk
     * (1 item akun piutang dagang) yang di-alokasikan ke faktur ini.
     */
    public function pelunasan(Penjualan $penjualan, Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'rekening_id' => 'required|exists:rekenings,id',
            'nominal' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($penjualan->status !== 'posted') {
            return back()->with('error', 'Hanya transaksi yang sudah diposting yang dapat menerima pelunasan.');
        }
        if ($penjualan->metode_bayar !== 'kredit') {
            return back()->with('error', 'Transaksi tunai tidak memiliki piutang yang dapat dilunasi.');
        }

        $sisa = (float) $penjualan->sisa_piutang;
        $nominal = (float) $data['nominal'];
        if ($sisa <= 0.005) {
            return back()->with('error', 'Piutang transaksi ini sudah lunas.');
        }
        if ($nominal > $sisa + 0.005) {
            return back()->withErrors(['nominal' => 'Nominal pelunasan melebihi sisa tagihan ('.formatRupiah($sisa).').'])->withInput();
        }

        DB::beginTransaction();
        try {
            $rekening = Rekening::findOrFail($data['rekening_id']);
            $customer = $penjualan->customer;

            PelunasanService::bayarPiutang(
                $customer,
                $nominal,
                $rekening->id,
                $data['tanggal'],
                $data['keterangan'] ?? 'Pelunasan '.$penjualan->nomor
            );

            DB::commit();

            return redirect()->route('penjualan.show', $penjualan)->with('success', 'Pelunasan piutang berhasil dicatat.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function void(Penjualan $penjualan)
    {
        if ($penjualan->status === 'pending') {
            $penjualan->update(['status' => 'draft']);

            return redirect()->route('penjualan.index')->with('success', 'Draft penjualan dibatalkan.');
        }

        if ($penjualan->retur()->where('status', 'posted')->exists()) {
            return back()->with('error', 'Penjualan tidak dapat dibatalkan karena masih memiliki retur yang aktif. Batalkan retur terlebih dahulu.');
        }

        if ($penjualan->metode_bayar === 'kredit' && BbPiutang::where('penjualan_id', $penjualan->id)->where('kredit', '>', 0.005)->exists()) {
            return back()->with('error', 'Penjualan tidak dapat dibatalkan karena sudah ada pelunasan (Kas Masuk). Batalkan kas masuk pelunasan terlebih dahulu.');
        }

        DB::beginTransaction();
        try {
            $jurnals = $penjualan->jurnal()->get();
            foreach ($jurnals as $jurnal) {
                JournalService::void($jurnal, 'Pembatalan penjualan');
            }

            // Reverse BB piutang: reversal per-baris untuk SEMUA baris yang
            // terhubung ke faktur ini (baris piutang awal + alokasi pembayaran),
            // dengan saldo berjalan yang saling meniadakan efek baris aslinya.
            if ($penjualan->metode_bayar === 'kredit') {
                $rows = BbPiutang::where('penjualan_id', $penjualan->id)
                    ->where('customer_id', $penjualan->customer_id)
                    ->orderBy('id')
                    ->get();

                if ($rows->isNotEmpty()) {
                    $lastSaldo = $this->saldoBbPiutangTerakhir($penjualan->customer_id);

                    foreach ($rows as $row) {
                        $lastSaldo += (float) $row->kredit - (float) $row->debit;

                        BbPiutang::create([
                            'customer_id' => $penjualan->customer_id,
                            'penjualan_id' => $row->penjualan_id,
                            'jurnal_id' => null,
                            'tanggal' => now()->toDateString(),
                            'keterangan' => 'BATAL: Penjualan '.$penjualan->nomor,
                            'debit' => (float) $row->kredit,
                            'kredit' => (float) $row->debit,
                            'saldo' => round($lastSaldo, 2),
                        ]);
                    }
                }
            }

            // Kembalikan stok barang & catat di BB Persediaan
            $gudangId = (int) ($penjualan->gudang_id ?? PengaturanSistemService::gudangPenjualan() ?? 0);
            foreach ($penjualan->items as $item) {
                if ($item->barang && $item->barang->tipe === 'barang') {
                    StockService::masukBarang(
                        $item->barang,
                        (float) $item->jumlah,
                        (float) ($item->hpp > 0 ? $item->hpp : $item->barang->harga_avg),
                        now()->toDateString(),
                        'BATAL Penjualan '.$penjualan->nomor,
                        $penjualan
                    );
                    if ($gudangId > 0) {
                        StokGudangService::tambah($item->barang, $gudangId, (float) $item->jumlah);
                    }
                }
            }

            $penjualan->update(['status' => 'draft']);
            DB::commit();

            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dibatalkan.');
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

    private function akunPenjualan(): int
    {
        return (int) (PengaturanSistemService::akunId('penjualan') ?? 0);
    }

    private function akunPpnKeluaran(): int
    {
        return (int) (PengaturanSistemService::akunId('ppn_keluaran') ?? 0);
    }

    private function akunBebanTransport(): int
    {
        $id = PengaturanSistemService::akunId('beban_transport');

        if (! $id) {
            throw new \RuntimeException('Akun Beban Transportasi (524) tidak ditemukan di chart of accounts.');
        }

        return $id;
    }

    private function akunPiutangDagang(): int
    {
        return (int) (PengaturanSistemService::akunId('piutang') ?? 0);
    }

    private function catatBbPiutang(Customer $customer, $jurnal, string $tanggal, string $ket, float $debit, float $kredit, ?Penjualan $penjualan = null): void
    {
        $saldoTerakhir = $this->saldoBbPiutangTerakhir($customer->id);
        BbPiutang::create([
            'customer_id' => $customer->id,
            'penjualan_id' => $penjualan?->id,
            'jurnal_id' => $jurnal->id,
            'tanggal' => $tanggal,
            'keterangan' => $ket,
            'debit' => $debit,
            'kredit' => $kredit,
            'saldo' => $saldoTerakhir + $debit - $kredit,
        ]);
    }

    private function saldoBbPiutangTerakhir(int $customerId): float
    {
        $last = BbPiutang::where('customer_id', $customerId)->orderByDesc('id')->value('saldo');

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

    private function hargaTerakhirJual(): array
    {
        $rows = PenjualanItem::query()
            ->select(
                'penjualan_items.barang_id',
                'penjualan_items.harga_satuan',
                'penjualans.customer_id',
                'penjualans.nomor',
                'penjualans.tanggal'
            )
            ->join('penjualans', 'penjualans.id', '=', 'penjualan_items.penjualan_id')
            ->where('penjualans.status', 'posted')
            ->orderByDesc('penjualans.tanggal')
            ->orderByDesc('penjualans.id')
            ->get();

        $map = [];
        foreach ($rows as $r) {
            if (! isset($map[$r->customer_id][$r->barang_id])) {
                $map[$r->customer_id][$r->barang_id] = [
                    'harga' => (float) $r->harga_satuan,
                    'ref' => $r->nomor,
                    'tanggal' => $r->tanggal,
                ];
            }
        }

        return $map;
    }

    private function histDaftarHargaJual(): array
    {
        return DaftarHarga::query()
            ->where('entitas', 'customer')
            ->where('is_aktif', false)
            ->whereNotNull('tanggal_selesai')
            ->orderByDesc('tanggal_selesai')
            ->get()
            ->groupBy(fn ($d) => $d->customer_id.':'.$d->barang_id)
            ->map(fn ($g) => $g->take(5)->map(fn ($d) => [
                'harga' => (float) $d->harga,
                'tanggal' => $d->tanggal_selesai ? $d->tanggal_selesai->format('d/m/Y') : null,
            ]))
            ->toArray();
    }

    private function buildPriceOptsJual(array $hargaMap, array $barangHargaMap, array $lastJualMap): array
    {
        $hist = $this->histDaftarHargaJual();

        $keys = [];
        foreach ($hargaMap as $custId => $barangs) {
            foreach (array_keys($barangs ?? []) as $barangId) {
                $keys[$custId][$barangId] = true;
            }
        }
        foreach ($lastJualMap as $custId => $items) {
            foreach (array_keys($items ?? []) as $barangId) {
                $keys[$custId][$barangId] = true;
            }
        }
        foreach ($hist as $key => $_) {
            [$custId, $barangId] = array_pad(explode(':', (string) $key), 2, null);
            if ($custId !== null && $barangId !== null) {
                $keys[(int) $custId][(int) $barangId] = true;
            }
        }

        $opts = [];
        foreach ($keys as $custId => $barangs) {
            foreach (array_keys($barangs) as $barangId) {
                $tiers = (is_array($hargaMap[$custId] ?? null) && is_array($hargaMap[$custId][$barangId] ?? null))
                    ? $hargaMap[$custId][$barangId]
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

                $last = $lastJualMap[$custId][$barangId] ?? null;
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

                foreach (($hist[$custId.':'.$barangId] ?? []) as $j => $h) {
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

                $opts[$custId][$barangId] = $list;
            }
        }

        return $opts;
    }
}
