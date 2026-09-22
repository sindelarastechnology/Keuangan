<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\JurnalUmum;
use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Penyusutan;
use App\Models\PeriodeAkuntansi;
use App\Models\Rekening;
use App\Models\Supplier;
use App\Services\PeriodeService;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ── Periode & rentang tanggal ─────────────────────────────────────
        $periode = PeriodeService::aktif();
        $preset = $request->input('preset', 'bulan_ini');

        [$dari, $sampai] = $this->resolveRentang($request, $periode, $preset);

        // Bulan sebelumnya untuk perbandingan
        $dariLalu = Carbon::parse($dari)->subMonth()->startOfMonth()->toDateString();
        $sampaiLalu = Carbon::parse($dari)->subMonth()->endOfMonth()->toDateString();

        // ── KPI Utama ─────────────────────────────────────────────────────
        // Saldo kas & bank — dihitung sekali dari seluruh rekening aktif
        $saldoRekening = Rekening::aktif()->get()->sum(fn ($r) => $r->saldo);

        $userId = (int) auth()->id();

        // Penjualan & pembelian periode ini vs lalu (hanya status posted; draft tidak memengaruhi keuangan)
        $totalPenjualan = (float) Penjualan::where('status', 'posted')->whereBetween('tanggal', [$dari, $sampai])->sum('total');
        $totalPenjualanLalu = (float) Penjualan::where('status', 'posted')->whereBetween('tanggal', [$dariLalu, $sampaiLalu])->sum('total');

        $totalPembelian = (float) Pembelian::where('status', 'posted')->whereBetween('tanggal', [$dari, $sampai])->sum('total');
        $totalPembelianLalu = (float) Pembelian::where('status', 'posted')->whereBetween('tanggal', [$dariLalu, $sampaiLalu])->sum('total');

        // Draft (pending) periode ini — ditampilkan sebagai info, tidak dijumlahkan ke KPI
        $draftPenjualan = [
            'count' => (int) Penjualan::where('status', 'pending')->whereBetween('tanggal', [$dari, $sampai])->count(),
            'total' => (float) Penjualan::where('status', 'pending')->whereBetween('tanggal', [$dari, $sampai])->sum('total'),
        ];
        $draftPembelian = [
            'count' => (int) Pembelian::where('status', 'pending')->whereBetween('tanggal', [$dari, $sampai])->count(),
            'total' => (float) Pembelian::where('status', 'pending')->whereBetween('tanggal', [$dari, $sampai])->sum('total'),
        ];

        $kasMasukTotal = (float) KasMasuk::whereBetween('tanggal', [$dari, $sampai])->sum('grand_total')
            + (float) Penjualan::where('status', 'posted')->where('metode_bayar', 'tunai')->whereBetween('tanggal', [$dari, $sampai])->sum('total');
        $kasMasukLalu = (float) KasMasuk::whereBetween('tanggal', [$dariLalu, $sampaiLalu])->sum('grand_total')
            + (float) Penjualan::where('status', 'posted')->where('metode_bayar', 'tunai')->whereBetween('tanggal', [$dariLalu, $sampaiLalu])->sum('total');

        $kasKeluarTotal = (float) KasKeluar::whereBetween('tanggal', [$dari, $sampai])->sum('grand_total')
            + (float) Pembelian::where('status', 'posted')->where('metode_bayar', 'tunai')->whereBetween('tanggal', [$dari, $sampai])->sum('total');
        $kasKeluarLalu = (float) KasKeluar::whereBetween('tanggal', [$dariLalu, $sampaiLalu])->sum('grand_total')
            + (float) Pembelian::where('status', 'posted')->where('metode_bayar', 'tunai')->whereBetween('tanggal', [$dariLalu, $sampaiLalu])->sum('total');

        // Piutang & hutang — optimasi: gunakan subquery MAX saldo per entitas
        $totalPiutang = (float) DB::table('bb_piutang as a')
            ->joinSub(
                DB::table('bb_piutang')->selectRaw('customer_id, MAX(id) as last_id')->where('user_id', $userId)->groupBy('customer_id'),
                'last', fn ($j) => $j->on('a.id', '=', 'last.last_id')
            )
            ->where('a.user_id', $userId)
            ->whereExists(fn ($q) => $q->from('customers')->whereColumn('customers.id', 'a.customer_id')->where('customers.is_aktif', true))
            ->sum('a.saldo');

        $totalHutang = (float) DB::table('bb_hutang as a')
            ->joinSub(
                DB::table('bb_hutang')->selectRaw('supplier_id, MAX(id) as last_id')->where('user_id', $userId)->groupBy('supplier_id'),
                'last', fn ($j) => $j->on('a.id', '=', 'last.last_id')
            )
            ->where('a.user_id', $userId)
            ->whereExists(fn ($q) => $q->from('suppliers')->whereColumn('suppliers.id', 'a.supplier_id')->where('suppliers.is_aktif', true))
            ->sum('a.saldo');

        // Laba Rugi
        $labaRugi = ReportService::labaRugi($dari, $sampai);

        // ── Tren 6 bulan ─────────────────────────────────────────────────
        $tren = $this->buildTren6Bulan($dari);

        // ── Actionable: hal yang butuh perhatian ─────────────────────────

        // Transaksi pending approval
        $pendingApproval = $this->getPendingApproval();

        // Stok kritis (barang aktif, stok <= min_stok)
        $stokKritis = Barang::aktif()->barang()
            ->whereColumn('stok', '<=', 'min_stok')
            ->where('min_stok', '>', 0)
            ->orderByRaw('(stok / NULLIF(min_stok, 0)) ASC')
            ->limit(6)
            ->get(['id', 'nama', 'kode', 'stok', 'min_stok', 'satuan']);

        // Stok habis (stok <= 0)
        $stokHabis = Barang::aktif()->barang()
            ->where('stok', '<=', 0)
            ->count();

        // Piutang per customer (top 5 terbesar)
        $topPiutang = DB::table('bb_piutang as a')
            ->joinSub(
                DB::table('bb_piutang')->selectRaw('customer_id, MAX(id) as last_id')->where('user_id', $userId)->groupBy('customer_id'),
                'last', fn ($j) => $j->on('a.id', '=', 'last.last_id')
            )
            ->join('customers', 'customers.id', '=', 'a.customer_id')
            ->where('a.user_id', $userId)
            ->where('a.saldo', '>', 0)
            ->where('customers.is_aktif', true)
            ->orderByDesc('a.saldo')
            ->limit(5)
            ->select('customers.id', 'customers.nama', 'a.saldo')
            ->get();

        // Hutang per supplier (top 5 terbesar)
        $topHutang = DB::table('bb_hutang as a')
            ->joinSub(
                DB::table('bb_hutang')->selectRaw('supplier_id, MAX(id) as last_id')->where('user_id', $userId)->groupBy('supplier_id'),
                'last', fn ($j) => $j->on('a.id', '=', 'last.last_id')
            )
            ->join('suppliers', 'suppliers.id', '=', 'a.supplier_id')
            ->where('a.user_id', $userId)
            ->where('a.saldo', '>', 0)
            ->where('suppliers.is_aktif', true)
            ->orderByDesc('a.saldo')
            ->limit(5)
            ->select('suppliers.id', 'suppliers.nama', 'a.saldo')
            ->get();

        // Kredit jatuh tempo (pembelian kredit yang masih berbaki dalam 30 hari ke depan)
        $pembelianJatuhTempo = DB::table('pembelians as p')
            ->joinSub(
                DB::table('bb_hutang')->selectRaw('pembelian_id, MAX(id) as last_id')->where('user_id', $userId)->groupBy('pembelian_id'),
                'last',
                fn ($j) => $j->on('p.id', '=', 'last.pembelian_id')
            )
            ->join('bb_hutang as b', 'b.id', '=', 'last.last_id')
            ->join('suppliers', 'suppliers.id', '=', 'p.supplier_id')
            ->where('p.user_id', $userId)
            ->where('b.user_id', $userId)
            ->where('p.metode_bayar', 'kredit')
            ->where('p.status', 'posted')
            ->whereBetween('p.tanggal', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->where('b.saldo', '>', 0)
            ->select('p.id', 'p.nomor', 'p.tanggal', 'p.total', 'p.supplier_id', 'suppliers.nama as supplier_nama', 'b.saldo')
            ->orderBy('p.tanggal')
            ->orderByDesc('b.saldo')
            ->limit(5)
            ->get();

        // Status periode akuntansi berjalan
        $periodeStatus = $this->getPeriodeStatus();

        // Top barang terlaku (berdasarkan qty terjual di periode ini)
        $topBarang = DB::table('penjualan_items')
            ->join('penjualans', 'penjualans.id', '=', 'penjualan_items.penjualan_id')
            ->join('barang', 'barang.id', '=', 'penjualan_items.barang_id')
            ->where('penjualan_items.user_id', $userId)
            ->where('penjualans.user_id', $userId)
            ->where('barang.user_id', $userId)
            ->whereBetween('penjualans.tanggal', [$dari, $sampai])
            ->where('penjualans.status', 'posted')
            ->groupBy('penjualan_items.barang_id', 'barang.nama', 'barang.satuan', 'barang.kode')
            ->orderByRaw('SUM(penjualan_items.jumlah) DESC')
            ->limit(5)
            ->select(
                'penjualan_items.barang_id',
                'barang.nama',
                'barang.kode',
                'barang.satuan',
                DB::raw('SUM(penjualan_items.jumlah) as total_qty'),
                DB::raw('SUM(penjualan_items.subtotal) as total_nilai')
            )
            ->get();

        // Penyusutan bulan ini
        $bulanIni = now()->format('Y-m');
        $penyusutanBulanIni = (float) Penyusutan::where('periode', $bulanIni)->sum('beban');
        $jumlahAsetAktif = Aset::where('status', 'aktif')->count();

        // Jurnal draft / pending
        $jurnalDraft = JurnalUmum::where('approval_status', 'draft')->count();

        // Transaksi terakhir (draft yang sudah dibatalkan tidak ditampilkan)
        $penjualanTerakhir = Penjualan::with('customer:id,nama')->where('status', '!=', 'draft')->latest()->take(5)->get(['id', 'nomor', 'tanggal', 'total', 'metode_bayar', 'customer_id', 'status']);
        $pembelianTerakhir = Pembelian::with('supplier:id,nama')->where('status', '!=', 'draft')->latest()->take(5)->get(['id', 'nomor', 'tanggal', 'total', 'metode_bayar', 'supplier_id', 'status']);

        return view('dashboard', compact(
            'periode', 'periodeStatus', 'dari', 'sampai', 'preset',
            'saldoRekening',
            'totalPenjualan', 'totalPenjualanLalu',
            'totalPembelian', 'totalPembelianLalu',
            'draftPenjualan', 'draftPembelian',
            'kasMasukTotal', 'kasMasukLalu',
            'kasKeluarTotal', 'kasKeluarLalu',
            'totalPiutang', 'totalHutang',
            'labaRugi',
            'tren',
            'pendingApproval',
            'stokKritis', 'stokHabis',
            'topPiutang', 'topHutang',
            'pembelianJatuhTempo',
            'topBarang',
            'penyusutanBulanIni', 'jumlahAsetAktif',
            'jurnalDraft',
            'penjualanTerakhir', 'pembelianTerakhir'
        ));
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function resolveRentang(Request $request, ?object $periode, string $preset): array
    {
        if ($request->filled('dari') && $request->filled('sampai')) {
            return [$request->dari, $request->sampai];
        }

        return match ($preset) {
            'minggu_ini' => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
            'bulan_lalu' => [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()],
            'kuartal_ini' => [now()->firstOfQuarter()->toDateString(), now()->lastOfQuarter()->toDateString()],
            'tahun_ini' => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
            default => $periode
                ? PeriodeService::rentang($periode)
                : [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
        };
    }

    /**
     * Tren 6 bulan terakhir: penjualan, pembelian, kas masuk, kas keluar — single pass per bulan.
     */
    private function buildTren6Bulan(string $referensiDari): array
    {
        $tren = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = Carbon::parse($referensiDari)->subMonths($i)->startOfMonth();
            $awal = $bulan->toDateString();
            $akhir = $bulan->copy()->endOfMonth()->toDateString();

            $penjualan = (float) Penjualan::where('status', 'posted')->whereBetween('tanggal', [$awal, $akhir])->sum('total');
            $pembelian = (float) Pembelian::where('status', 'posted')->whereBetween('tanggal', [$awal, $akhir])->sum('total');
            $masuk = (float) KasMasuk::whereBetween('tanggal', [$awal, $akhir])->sum('grand_total')
                + (float) Penjualan::where('status', 'posted')->where('metode_bayar', 'tunai')->whereBetween('tanggal', [$awal, $akhir])->sum('total');
            $keluar = (float) KasKeluar::whereBetween('tanggal', [$awal, $akhir])->sum('grand_total')
                + (float) Pembelian::where('status', 'posted')->where('metode_bayar', 'tunai')->whereBetween('tanggal', [$awal, $akhir])->sum('total');

            $tren[] = [
                'bulan' => $bulan->translatedFormat('M'),
                'bulan_full' => $bulan->translatedFormat('F Y'),
                'penjualan' => $penjualan,
                'pembelian' => $pembelian,
                'masuk' => $masuk,
                'keluar' => $keluar,
            ];
        }

        return $tren;
    }

    /**
     * Kumpulkan transaksi pending approval lintas modul.
     */
    private function getPendingApproval(): array
    {
        $items = [];

        $kasMasukPending = KasMasuk::where('approval_status', 'pending_review')->count();
        if ($kasMasukPending > 0) {
            $items[] = ['label' => 'Kas Masuk menunggu approval', 'count' => $kasMasukPending, 'route' => 'kas-masuk.index', 'color' => 'emerald'];
        }

        $kasKeluarPending = KasKeluar::where('approval_status', 'pending_review')->count();
        if ($kasKeluarPending > 0) {
            $items[] = ['label' => 'Kas Keluar menunggu approval', 'count' => $kasKeluarPending, 'route' => 'kas-keluar.index', 'color' => 'red'];
        }

        $penjualanPending = Penjualan::where('approval_status', 'pending_review')->count();
        if ($penjualanPending > 0) {
            $items[] = ['label' => 'Penjualan menunggu approval', 'count' => $penjualanPending, 'route' => 'penjualan.index', 'color' => 'blue'];
        }

        $pembelianPending = Pembelian::where('approval_status', 'pending_review')->count();
        if ($pembelianPending > 0) {
            $items[] = ['label' => 'Pembelian menunggu approval', 'count' => $pembelianPending, 'route' => 'pembelian.index', 'color' => 'amber'];
        }

        $jurnalPending = JurnalUmum::where('approval_status', 'pending_review')->count();
        if ($jurnalPending > 0) {
            $items[] = ['label' => 'Jurnal Manual menunggu approval', 'count' => $jurnalPending, 'route' => 'jurnal.index', 'color' => 'violet'];
        }

        return $items;
    }

    /**
     * Status periode: yang sedang berjalan, belum dibuka, dll.
     */
    private function getPeriodeStatus(): array
    {
        $now = now();
        $bulanIni = str_pad((string) $now->month, 2, '0', STR_PAD_LEFT);
        $tahunIni = (string) $now->year;

        $periodeIni = PeriodeAkuntansi::where('bulan', $bulanIni)->where('tahun', $tahunIni)->first();
        $periodeAktif = PeriodeService::aktif();

        return [
            'periode_ini' => $periodeIni,
            'periode_aktif' => $periodeAktif,
            'belum_dibuka' => ! $periodeAktif,
            'terkunci' => $periodeAktif?->is_locked ?? false,
            'label_aktif' => $periodeAktif?->label,
        ];
    }
}
