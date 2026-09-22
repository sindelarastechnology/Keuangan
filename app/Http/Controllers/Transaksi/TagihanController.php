<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Models\BbHutang;
use App\Models\BbPiutang;
use App\Models\Customer;
use App\Models\Rekening;
use App\Models\Supplier;
use App\Services\HutangAttributionService;
use App\Services\PelunasanService;
use App\Services\PiutangAttributionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Hub tagihan: satu tempat untuk memantau dan membayar semua piutang/hutang.
 *
 * Semua pembayaran memakai PelunasanService supaya akun, jurnal, dan alokasi
 * FIFO konsisten dengan pintu masuk lainnya (detail transaksi, kas masuk/keluar).
 */
class TagihanController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'tagihan_piutang_kolom';

    public const KOLOM_KUNCI_HUTANG = 'tagihan_hutang_kolom';

    public const KOLOM_OPTIONS = [
        'kode' => 'Kode',
        'nama' => 'Nama',
        'faktur' => 'Faktur Terbuka',
        'sisa' => 'Sisa',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['kode', 'nama', 'faktur', 'sisa'];

    public const KOLOM_WAJIB = ['aksi'];

    public function piutang(): View
    {
        $rows = $this->rekananBerbakiPiutang();
        $total = $rows->sum('sisa');

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif(self::KOLOM_KUNCI);

        return view('tagihan.piutang', compact('rows', 'total', 'kolomOptions', 'kolomAktif'));
    }

    public function simpanKolomPiutang(Request $request)
    {
        return $this->simpanKolom($request, self::KOLOM_KUNCI);
    }

    public function hutang(): View
    {
        $rows = $this->rekananBerbakiHutang();
        $total = $rows->sum('sisa');

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif(self::KOLOM_KUNCI_HUTANG);

        return view('tagihan.hutang', compact('rows', 'total', 'kolomOptions', 'kolomAktif'));
    }

    public function simpanKolomHutang(Request $request)
    {
        return $this->simpanKolom($request, self::KOLOM_KUNCI_HUTANG);
    }

    public function formBayarPiutang(Customer $customer, Request $request): RedirectResponse|View
    {
        $sisa = (float) $customer->saldoPiutang;
        if ($sisa <= 0.005) {
            return redirect()->route('tagihan.piutang')->with('error', "Customer {$customer->nama} tidak memiliki piutang yang belum lunas.");
        }

        $faktur = PiutangAttributionService::fakturBelumLunas($customer);
        $fakturIdRequested = (int) $request->get('faktur', 0);
        $f = $fakturIdRequested ? $faktur->firstWhere('id', $fakturIdRequested) : null;
        $nominal = $this->nominalAwal((float) $request->get('nominal'), $fakturIdRequested, $faktur, $sisa);
        $selectedFakturId = $f?->id;
        $rekeningList = Rekening::aktif()->orderBy('nama')->get();

        return view('tagihan.bayar-piutang', compact('customer', 'sisa', 'faktur', 'nominal', 'rekeningList', 'selectedFakturId'));
    }

    public function formBayarHutang(Supplier $supplier, Request $request): RedirectResponse|View
    {
        $sisa = (float) $supplier->saldoHutang;
        if ($sisa <= 0.005) {
            return redirect()->route('tagihan.hutang')->with('error', "Supplier {$supplier->nama} tidak memiliki hutang yang belum lunas.");
        }

        $faktur = HutangAttributionService::fakturBelumLunas($supplier);
        $fakturIdRequested = (int) $request->get('faktur', 0);
        $f = $fakturIdRequested ? $faktur->firstWhere('id', $fakturIdRequested) : null;
        $nominal = $this->nominalAwal((float) $request->get('nominal'), $fakturIdRequested, $faktur, $sisa);
        $selectedFakturId = $f?->id;
        $rekeningList = Rekening::aktif()->orderBy('nama')->get();

        return view('tagihan.bayar-hutang', compact('supplier', 'sisa', 'faktur', 'nominal', 'rekeningList', 'selectedFakturId'));
    }

    public function bayarPiutang(Customer $customer, Request $request): RedirectResponse
    {
        $data = $this->validasiBayar($request);

        $sisa = (float) $customer->saldoPiutang;
        if ($sisa <= 0.005) {
            return back()->with('error', 'Customer ini tidak memiliki piutang yang belum lunas.');
        }

        $nominal = (float) $data['nominal'];
        if ($nominal > $sisa + 0.005) {
            return back()->withErrors(['nominal' => 'Nominal pembayaran melebihi total piutang ('.formatRupiah($sisa).').'])->withInput();
        }

        try {
            $kasMasuk = PelunasanService::bayarPiutang(
                $customer,
                $nominal,
                (int) $data['rekening_id'],
                $data['tanggal'],
                $data['keterangan'] ?? 'Pelunasan piutang '.$customer->nama
            );

            return redirect()->route('tagihan.piutang')
                ->with('success', 'Pembayaran piutang dari '.$customer->nama.' sebesar '.formatRupiah($nominal).' tercatat sebagai Kas Masuk '.$kasMasuk->nomor.'.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function bayarHutang(Supplier $supplier, Request $request): RedirectResponse
    {
        $data = $this->validasiBayar($request);

        $sisa = (float) $supplier->saldoHutang;
        if ($sisa <= 0.005) {
            return back()->with('error', 'Supplier ini tidak memiliki hutang yang belum lunas.');
        }

        $nominal = (float) $data['nominal'];
        if ($nominal > $sisa + 0.005) {
            return back()->withErrors(['nominal' => 'Nominal pembayaran melebihi total hutang ('.formatRupiah($sisa).').'])->withInput();
        }

        try {
            $kasKeluar = PelunasanService::bayarHutang(
                $supplier,
                $nominal,
                (int) $data['rekening_id'],
                $data['tanggal'],
                $data['keterangan'] ?? 'Pelunasan hutang '.$supplier->nama
            );

            $redirect = redirect()->route('tagihan.hutang')
                ->with('success', 'Pembayaran hutang ke '.$supplier->nama.' sebesar '.formatRupiah($nominal).' tercatat sebagai Kas Keluar '.$kasKeluar->nomor.'.');

            $rekening = Rekening::find((int) $data['rekening_id']);
            if ($rekening && (float) $rekening->saldo < $nominal) {
                return $redirect->with('warning', 'Saldo rekening tidak mencukupi ('.$rekening->nama.': '.formatRupiah($rekening->saldo).'). Pembayaran tetap dicatat, namun saldo rekening menjadi minus.');
            }

            return $redirect;
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    private function validasiBayar(Request $request): array
    {
        return $request->validate([
            'tanggal' => 'required|date',
            'rekening_id' => 'required|exists:rekenings,id',
            'nominal' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string|max:500',
        ]);
    }

    /**
     * Rekanan (customer) yang masih punya piutang terbuka (saldo ledger > 0).
     *
     * @return Collection<int, array{customer: Customer, sisa: float, jumlah_faktur: int}>
     */
    private function rekananBerbakiPiutang(): Collection
    {
        $lastIds = BbPiutang::query()->selectRaw('MAX(id) as last_id')->groupBy('customer_id')->pluck('last_id');

        return BbPiutang::whereIn('id', $lastIds)
            ->where('saldo', '>', 0.005)
            ->with('customer')
            ->get()
            ->map(fn (BbPiutang $r) => [
                'customer' => $r->customer,
                'sisa' => (float) $r->saldo,
                'jumlah_faktur' => PiutangAttributionService::fakturBelumLunas($r->customer)->count(),
            ])
            ->sortByDesc('sisa')
            ->values();
    }

    /**
     * Rekanan (supplier) yang masih punya hutang terbuka (saldo ledger > 0).
     *
     * @return Collection<int, array{supplier: Supplier, sisa: float, jumlah_faktur: int}>
     */
    private function rekananBerbakiHutang(): Collection
    {
        $lastIds = BbHutang::query()->selectRaw('MAX(id) as last_id')->groupBy('supplier_id')->pluck('last_id');

        return BbHutang::whereIn('id', $lastIds)
            ->where('saldo', '>', 0.005)
            ->with('supplier')
            ->get()
            ->map(fn (BbHutang $r) => [
                'supplier' => $r->supplier,
                'sisa' => (float) $r->saldo,
                'jumlah_faktur' => HutangAttributionService::fakturBelumLunas($r->supplier)->count(),
            ])
            ->sortByDesc('sisa')
            ->values();
    }

    /**
     * Nominal awal form pembayaran: prioritas nilai/nominal yang dikirim dari
     * daftar transaksi (?faktur=ID) agar terisi sisa faktur itu; fallback ke
     * total sisa rekanan.
     */
    private function nominalAwal(float $dariQuery, int $fakturId, Collection $faktur, float $sisa): float
    {
        $nominal = $dariQuery > 0 ? $dariQuery : $sisa;

        if ($fakturId) {
            $f = $faktur->firstWhere('id', $fakturId);
            if ($f) {
                $nominal = (float) $f->sisa_piutang;
            }
        }

        return round(min(max($nominal, 0.01), $sisa), 2);
    }
}
