<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\AturKolomTabel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\StoreKasMasukRequest;
use App\Models\AkunPerkiraan;
use App\Models\Customer;
use App\Models\KasMasuk;
use App\Models\Pajak;
use App\Models\Rekening;
use App\Services\JournalService;
use App\Services\NomorGenerator;
use App\Services\PengaturanSistemService;
use App\Services\PiutangAttributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class KasMasukController extends Controller
{
    use AturKolomTabel;

    public const KOLOM_KUNCI = 'kas_masuk_kolom';

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
        $kasMasuk = KasMasuk::with(['rekening', 'customer'])
            ->when($request->filled('dari'), fn ($q) => $q->where('tanggal', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->where('tanggal', '<=', $request->sampai))
            ->orderByDesc('tanggal')
            ->paginate(15)->withQueryString();

        $kolomOptions = self::KOLOM_OPTIONS;
        $kolomAktif = $this->kolomAktif();

        return view('transaksi.kas-masuk.index', compact('kasMasuk', 'kolomOptions', 'kolomAktif'));
    }

    public function create()
    {
        $rekeningList = Rekening::aktif()->orderBy('nama')->get();
        $akunList = AkunPerkiraan::leaf()->orderBy('kode')->get();
        $pajakList = Pajak::aktif()->get();
        $customers = Customer::aktif()->orderBy('nama')->get();
        $katList = $this->kategoriList();
        foreach ($katList as &$kat) {
            $kat['akun_id'] = $kat['role'] ? PengaturanSistemService::akunId($kat['role']) : null;
        }
        unset($kat);

        return view('transaksi.kas-masuk.create', compact('rekeningList', 'akunList', 'pajakList', 'katList', 'customers'));
    }

    /**
     * Daftar jenis transaksi sehari-hari untuk pemakai non-akuntan.
     * Setiap kategori di-mapping otomatis ke akun kredit yang benar.
     */
    protected function kategoriList()
    {
        return [
            ['value' => 'penjualan',  'label' => 'Hasil Penjualan / Hasil Usaha', 'role' => 'penjualan'],
            ['value' => 'jasa',       'label' => 'Hasil Jasa / Pekerjaan',        'role' => 'penjualan_jasa'],
            ['value' => 'piutang',    'label' => 'Pelunasan Piutang dari Customer', 'role' => 'piutang'],
            ['value' => 'pendapatan', 'label' => 'Pendapatan Lain-Lain',          'role' => 'pendapatan_lain'],
            ['value' => 'modal',      'label' => 'Setoran Modal Pemilik',         'role' => 'modal'],
            ['value' => 'bunga',      'label' => 'Pinjaman / Utang Diterima',     'role' => 'utang'],
            ['value' => 'manual',     'label' => 'Lainnya — Pilih Akun Sendiri',  'role' => null],
        ];
    }

    public function store(StoreKasMasukRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $rekening = Rekening::findOrFail($data['rekening_id']);
            $pajak = isset($data['pajak_id']) ? Pajak::find($data['pajak_id']) : null;

            $totalItems = 0;
            $jurnalItems = [];
            $itemRows = [];
            $akunPiutangId = (int) (PengaturanSistemService::akunId('piutang') ?? 0);
            $bbPiutangNominal = 0;

            foreach ($data['items'] as $item) {
                $nominal = (float) $item['nominal'];
                if ($nominal <= 0) {
                    continue;
                }
                $jurnalItems[] = [
                    'akun_id' => $item['akun_id'],
                    'debit' => 0,
                    'kredit' => $nominal,
                    'keterangan' => $item['keterangan'] ?? null,
                ];
                $itemRows[] = $item + ['nominal' => $nominal];
                $totalItems += $nominal;
                if ($akunPiutangId && (int) $item['akun_id'] === $akunPiutangId) {
                    $bbPiutangNominal += $nominal;
                }
            }

            $pajakNominal = $pajak ? $totalItems * ($pajak->rate / 100) : 0;
            $grandTotal = $totalItems + $pajakNominal;

            // Validasi pelunasan piutang: wajib customer dan tidak boleh melebihi sisa piutangnya.
            if ($bbPiutangNominal > 0) {
                $customer = isset($data['customer_id']) ? Customer::find($data['customer_id']) : null;

                if (! $customer) {
                    throw new RuntimeException('Item pelunasan piutang memerlukan customer. Pilih customer terlebih dahulu.');
                }

                $sisa = (float) $customer->saldoPiutang;
                if ($bbPiutangNominal > $sisa + 0.005) {
                    throw new RuntimeException('Nominal pelunasan melebihi total piutang '.$customer->nama.' ('.formatRupiah($sisa).').');
                }
            }

            // Pajak PPN Keluaran
            if ($pajakNominal > 0) {
                $akunPpn = PengaturanSistemService::akunId('ppn_keluaran');
                if ($akunPpn) {
                    $jurnalItems[] = [
                        'akun_id' => $akunPpn,
                        'debit' => 0,
                        'kredit' => $pajakNominal,
                        'keterangan' => 'PPN '.($pajak->nama ?? ''),
                    ];
                }
            }

            // Debit kas/bank sebesar grand total
            $jurnalItems[] = [
                'akun_id' => $rekening->akun_id,
                'debit' => $grandTotal,
                'kredit' => 0,
            ];

            // Simpan header
            $kasMasuk = new KasMasuk;
            $kasMasuk->nomor = NomorGenerator::generate('KM', $data['tanggal']);
            $kasMasuk->tanggal = $data['tanggal'];
            $kasMasuk->rekening_id = $data['rekening_id'];
            $kasMasuk->customer_id = $data['customer_id'] ?? null;
            $kasMasuk->keterangan = $data['keterangan'] ?? null;
            $kasMasuk->total = $totalItems;
            $kasMasuk->pajak_id = $data['pajak_id'] ?? null;
            $kasMasuk->pajak_nominal = $pajakNominal;
            $kasMasuk->grand_total = $grandTotal;
            $kasMasuk->created_by = auth()->id();
            $kasMasuk->save();

            foreach ($itemRows as $row) {
                $kasMasuk->items()->create($row);
            }

            // Post jurnal
            $jurnal = JournalService::post('kas_masuk', $data['tanggal'], $jurnalItems, $data['keterangan'] ?? 'Kas Masuk '.$kasMasuk->nomor, $kasMasuk);

            // Update BB Piutang jika ada item pelunasan piutang (akun 113) untuk customer
            if (! empty($data['customer_id']) && $bbPiutangNominal > 0) {
                $cust = Customer::find($data['customer_id']);
                if ($cust) {
                    PiutangAttributionService::alokasikan(
                        $cust,
                        $bbPiutangNominal,
                        'Pelunasan Kas Masuk '.$kasMasuk->nomor.($data['keterangan'] ? ' - '.$data['keterangan'] : ''),
                        $data['tanggal'],
                        $jurnal->id
                    );
                }
            }

            DB::commit();

            return redirect()->route('kas-masuk.show', $kasMasuk)->with('success', 'Kas masuk berhasil dicatat.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(KasMasuk $kasMasuk)
    {
        $kasMasuk->load(['rekening', 'customer', 'items.akun', 'pajak', 'jurnal.items.akun']);

        return view('transaksi.kas-masuk.show', compact('kasMasuk'));
    }

    public function destroy(KasMasuk $kasMasuk)
    {
        DB::beginTransaction();
        try {
            // Void jurnal
            if ($kasMasuk->jurnal) {
                JournalService::void($kasMasuk->jurnal, 'Pembatalan kas masuk');
            }

            // Reversal BB Piutang (balik nilai yang ter-atribusi utk jurnal ini) jika ada
            if ($kasMasuk->customer_id && $kasMasuk->jurnal) {
                $cust = Customer::find($kasMasuk->customer_id);
                if ($cust) {
                    PiutangAttributionService::batalkan(
                        $cust,
                        $kasMasuk->jurnal->id,
                        'BATAL Pelunasan Kas Masuk '.$kasMasuk->nomor,
                        now()->toDateString()
                    );
                }
            }

            $kasMasuk->items()->delete();
            $kasMasuk->delete();
            DB::commit();

            return redirect()->route('kas-masuk.index')->with('success', 'Kas masuk berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }
}
