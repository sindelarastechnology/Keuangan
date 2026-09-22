<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\StoreKasKeluarRequest;
use App\Models\AkunPerkiraan;
use App\Models\KasKeluar;
use App\Models\Pajak;
use App\Models\Rekening;
use App\Models\Supplier;
use App\Services\HutangAttributionService;
use App\Services\JournalService;
use App\Services\NomorGenerator;
use App\Services\PengaturanSistemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class KasKeluarController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'kas_keluar_kolom';

    public const KOLOM_OPTIONS = [
        'nomor' => 'Nomor',
        'tanggal' => 'Tanggal',
        'keterangan' => 'Keterangan',
        'total' => 'Total',
        'aksi' => 'Aksi',
    ];

    public const KOLOM_DEFAULT = ['nomor', 'tanggal', 'keterangan', 'total'];

    public const KOLOM_WAJIB = ['aksi'];

    public function index(Request $request)
    {
        $kasKeluar = KasKeluar::with(['rekening', 'supplier'])
            ->when($request->filled('dari'), fn ($q) => $q->where('tanggal', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->where('tanggal', '<=', $request->sampai))
            ->orderByDesc('tanggal')
            ->paginate(15)->withQueryString();

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('transaksi.kas-keluar.index', compact('kasKeluar', 'kolomOptions', 'kolomAktif'));
    }

    public function create()
    {
        $rekeningList = Rekening::aktif()->orderBy('nama')->get();
        $akunList = AkunPerkiraan::leaf()->orderBy('kode')->get();
        $pajakList = Pajak::aktif()->get();
        $suppliers = Supplier::aktif()->orderBy('nama')->get();
        $saldoMap = $rekeningList->pluck('saldo', 'id');
        $namaMap = $rekeningList->pluck('nama', 'id');
        $katList = $this->kategoriList();
        foreach ($katList as &$kat) {
            if ($kat['role'] ?? null) {
                $kat['akun_id'] = PengaturanSistemService::akunId($kat['role']);
            } else {
                $kat['akun_id'] = $kat['kode'] ? (AkunPerkiraan::where('kode', $kat['kode'])->value('id') ?? null) : null;
            }
        }
        unset($kat);

        return view('transaksi.kas-keluar.create', compact('rekeningList', 'akunList', 'pajakList', 'katList', 'suppliers', 'saldoMap', 'namaMap'));
    }

    /**
     * Daftar jenis transaksi sehari-hari untuk pemakai non-akuntan.
     * Setiap kategori di-mapping otomatis ke akun debit yang benar.
     */
    protected function kategoriList()
    {
        return [
            ['value' => 'bahan',      'label' => 'Beli Bahan Baku / Barang Dagang', 'role' => 'persediaan', 'kode' => null],
            ['value' => 'utang',      'label' => 'Bayar Hutang ke Supplier',        'role' => 'utang', 'kode' => null],
            ['value' => 'gaji',       'label' => 'Bayar Gaji Karyawan',             'role' => 'beban_gaji', 'kode' => null],
            ['value' => 'operasional', 'label' => 'Operasional (Listrik, Air, dll)', 'role' => 'beban_operasional', 'kode' => null],
            ['value' => 'sewa',       'label' => 'Bayar Sewa',                      'role' => 'beban_sewa', 'kode' => null],
            ['value' => 'transport',  'label' => 'Transportasi / Pengiriman',       'role' => 'beban_transport', 'kode' => null],
            ['value' => 'aset',       'label' => 'Beli Aset (Peralatan, dll)',      'role' => 'aset_tetap', 'kode' => null],
            ['value' => 'manual',     'label' => 'Lainnya — Pilih Akun Sendiri',    'role' => null, 'kode' => null],
        ];
    }

    public function store(StoreKasKeluarRequest $request)
    {
        $data = $request->validated();

        $rekening = Rekening::findOrFail($data['rekening_id']);
        $pajak = isset($data['pajak_id']) ? Pajak::find($data['pajak_id']) : null;

        $totalItems = 0;
        $jurnalItems = [];
        $itemRows = [];
        $akunUtangId = (int) (PengaturanSistemService::akunId('utang') ?? 0);
        $bbHutangNominal = 0;

        foreach ($data['items'] as $item) {
            $nominal = (float) $item['nominal'];
            if ($nominal <= 0) {
                continue;
            }
            $jurnalItems[] = [
                'akun_id' => $item['akun_id'],
                'debit' => $nominal,
                'kredit' => 0,
                'keterangan' => $item['keterangan'] ?? null,
            ];
            $itemRows[] = $item + ['nominal' => $nominal];
            $totalItems += $nominal;
            if ($akunUtangId && (int) $item['akun_id'] === $akunUtangId) {
                $bbHutangNominal += $nominal;
            }
        }

        $pajakNominal = $pajak ? $totalItems * ($pajak->rate / 100) : 0;
        $grandTotal = $totalItems + $pajakNominal;

        // Saldo tidak mencukupi diperbolehkan (bukan error). Flag dipakai untuk pesan peringatan.
        $saldoKurang = (float) $rekening->saldo < $grandTotal;

        DB::beginTransaction();
        try {
            // Validasi pembayaran hutang: wajib supplier dan tidak boleh melebihi sisa hutangnya.
            if ($bbHutangNominal > 0) {
                $supplier = isset($data['supplier_id']) ? Supplier::find($data['supplier_id']) : null;

                if (! $supplier) {
                    throw new RuntimeException('Item pembayaran hutang memerlukan supplier. Pilih supplier terlebih dahulu.');
                }

                $sisa = (float) $supplier->saldoHutang;
                if ($bbHutangNominal > $sisa + 0.005) {
                    throw new RuntimeException('Nominal pembayaran melebihi total hutang '.$supplier->nama.' ('.formatRupiah($sisa).').');
                }
            }

            // Pajak PPN Masukan (213)
            if ($pajakNominal > 0) {
                $akunPpn = PengaturanSistemService::akunId('ppn_masukan');
                if ($akunPpn) {
                    $jurnalItems[] = [
                        'akun_id' => $akunPpn,
                        'debit' => $pajakNominal,
                        'kredit' => 0,
                        'keterangan' => 'PPN Masukan '.($pajak->nama ?? ''),
                    ];
                }
            }

            // Kredit kas/bank sebesar grand total
            $jurnalItems[] = [
                'akun_id' => $rekening->akun_id,
                'debit' => 0,
                'kredit' => $grandTotal,
            ];

            $kasKeluar = new KasKeluar;
            $kasKeluar->nomor = NomorGenerator::generate('KK', $data['tanggal']);
            $kasKeluar->tanggal = $data['tanggal'];
            $kasKeluar->rekening_id = $data['rekening_id'];
            $kasKeluar->supplier_id = $data['supplier_id'] ?? null;
            $kasKeluar->keterangan = $data['keterangan'] ?? null;
            $kasKeluar->total = $totalItems;
            $kasKeluar->pajak_id = $data['pajak_id'] ?? null;
            $kasKeluar->pajak_nominal = $pajakNominal;
            $kasKeluar->grand_total = $grandTotal;
            $kasKeluar->created_by = auth()->id();
            $kasKeluar->save();

            foreach ($itemRows as $row) {
                $kasKeluar->items()->create($row);
            }

            $jurnal = JournalService::post('kas_keluar', $data['tanggal'], $jurnalItems, $data['keterangan'] ?? 'Kas Keluar '.$kasKeluar->nomor, $kasKeluar);

            // Update BB Hutang jika ada item bayar utang (akun 211) untuk supplier
            if (! empty($data['supplier_id']) && $bbHutangNominal > 0) {
                $sup = Supplier::find($data['supplier_id']);
                if ($sup) {
                    HutangAttributionService::alokasikan(
                        $sup,
                        $bbHutangNominal,
                        'Pelunasan Kas Keluar '.$kasKeluar->nomor.($data['keterangan'] ? ' - '.$data['keterangan'] : ''),
                        $data['tanggal'],
                        $jurnal->id
                    );
                }
            }

            DB::commit();

            $redirect = redirect()->route('kas-keluar.show', $kasKeluar)->with('success', 'Kas keluar berhasil dicatat.');

            if ($saldoKurang) {
                return $redirect->with('warning', 'Saldo rekening tidak mencukupi ('.formatRupiah($rekening->saldo).'). Transaksi tetap dicatat, namun saldo rekening menjadi minus.');
            }

            return $redirect;
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(KasKeluar $kasKeluar)
    {
        $kasKeluar->load(['rekening', 'supplier', 'items.akun', 'pajak', 'jurnal.items.akun']);

        return view('transaksi.kas-keluar.show', compact('kasKeluar'));
    }

    public function destroy(KasKeluar $kasKeluar)
    {
        DB::beginTransaction();
        try {
            if ($kasKeluar->jurnal) {
                JournalService::void($kasKeluar->jurnal, 'Pembatalan kas keluar');
            }

            // Reversal BB Hutang (balik nilai yang ter-atribusi utk jurnal ini) jika ada
            if ($kasKeluar->supplier_id && $kasKeluar->jurnal) {
                $sup = $kasKeluar->supplier;
                if ($sup) {
                    HutangAttributionService::batalkan(
                        $sup,
                        $kasKeluar->jurnal->id,
                        'BATAL Pelunasan Kas Keluar '.$kasKeluar->nomor,
                        now()->toDateString()
                    );
                }
            }

            $kasKeluar->items()->delete();
            $kasKeluar->delete();
            DB::commit();

            return redirect()->route('kas-keluar.index')->with('success', 'Kas keluar berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }
}
