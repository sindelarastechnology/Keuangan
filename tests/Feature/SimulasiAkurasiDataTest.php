<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\Barang;
use App\Models\BbHutang;
use App\Models\BbPiutang;
use App\Models\Customer;
use App\Models\Gudang;
use App\Models\JurnalItem;
use App\Models\Pajak;
use App\Models\Rekening;
use App\Models\StokGudang;
use App\Models\Supplier;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SimulasiAkurasiDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::where('email', 'admin@keuangan.test')->first());
    }

    private function akunKode(string $kode): int
    {
        return (int) AkunPerkiraan::where('kode', $kode)->value('id');
    }

    private function rekeningKas(): Rekening
    {
        return Rekening::create([
            'jenis' => 'kas',
            'nama' => 'Kas Lokal',
            'akun_id' => $this->akunKode('111'),
            'saldo_awal' => 0,
            'is_aktif' => true,
        ]);
    }

    private function rekeningBank(): Rekening
    {
        return Rekening::create([
            'jenis' => 'bank',
            'nama' => 'Bank BCA',
            'akun_id' => $this->akunKode('112'),
            'saldo_awal' => 5_000_000,
            'is_aktif' => true,
        ]);
    }

    private function assertTrialBalanceSeimbang(string $pesan): void
    {
        $base = fn (string $kolom) => (float) JurnalItem::whereHas(
            'jurnal',
            fn ($q) => $q->where('is_posted', true)
        )->sum($kolom);

        $this->assertEqualsWithDelta($base('debit'), $base('kredit'), 0.01, "Trial balance $pesan");
    }

    private function assertStokGudangSeimbang(Barang $barang, string $pesan): void
    {
        $totalGudang = (float) StokGudang::where('barang_id', $barang->id)->sum('qty');
        $barang->refresh();
        $this->assertEqualsWithDelta((float) $barang->stok, $totalGudang, 0.01, "Sum stok_gudang == stok $pesan");
    }

    public function test_alur_lengkap_semua_modul_menjaga_invariant_akuntansi_dan_stok(): void
    {
        $sup = Supplier::create(['kode' => 'SUP-AKUR-'.substr((string) uniqid(), -5), 'nama' => 'PT Pemasok Akurat', 'is_aktif' => true]);
        $cust = Customer::create(['kode' => 'CUST-AKUR-'.substr((string) uniqid(), -5), 'nama' => 'PT Customer Akurat', 'is_aktif' => true]);
        $barang = Barang::create([
            'kode' => 'BRG-AKUR-'.substr((string) uniqid(), -5),
            'nama' => 'Barang Akurat',
            'tipe' => 'barang',
            'stok' => 0,
            'harga_beli' => 3000,
            'harga_jual' => 9000,
            'is_aktif' => true,
        ]);
        $gudangTujuan = Gudang::create(['kode' => 'GDG-CBG', 'nama' => 'Gudang Cabang', 'is_aktif' => true]);
        $gudangUtama = Gudang::utama();
        $pajak = Pajak::create(['nama' => 'PPN 11%', 'rate' => 11, 'is_aktif' => true]);
        $kas = $this->rekeningKas();
        $bank = $this->rekeningBank();
        $tanggal = now()->toDateString();
        $akun115 = $this->akunKode('115');

        $cek = fn () => [
            'stok' => (float) $barang->refresh()->stok,
            'persediaan' => (float) JurnalItem::where('akun_id', $akun115)
                ->whereHas('jurnal', fn ($q) => $q->where('is_posted', true))
                ->selectRaw('COALESCE(SUM(debit),0) - COALESCE(SUM(kredit),0) as s')
                ->value('s'),
        ];

        // 1. Pembelian kredit 20 @ 3.000 + PPN
        $this->post(route('pembelian.store'), [
            'tanggal' => $tanggal, 'supplier_id' => $sup->id, 'metode_bayar' => 'kredit',
            'diskon' => 0, 'diskon_tipe' => 'nominal', 'sync_harga' => null, 'pajak_id' => $pajak->id,
            'items' => [['barang_id' => $barang->id, 'jumlah' => 20, 'harga_satuan' => 3000, 'diskon' => 0]],
        ])->assertSessionHasNoErrors();
        $this->assertEquals(20, $cek()['stok']);
        $this->assertEquals(60_000, $cek()['persediaan']);
        $this->assertStokGudangSeimbang($barang, 'stlh pembelian');

        // 2. Perubahan stok rusak keluar 2
        $this->post(route('perubahan-stok.store'), [
            'tanggal' => $tanggal, 'jenis' => 'rusak',
            'items' => [['barang_id' => $barang->id, 'arah' => 'keluar', 'jumlah' => 2]],
        ])->assertSessionHasNoErrors();
        $this->assertEquals(18, $cek()['stok']);
        $this->assertEquals(54_000, $cek()['persediaan']);
        $this->assertStokGudangSeimbang($barang, 'stlh rusak');

        // 3. Stok opname fisik 18 (tanpa selisih) -> tidak mengubah saldo
        $this->post(route('stok-opname.store'), [
            'tanggal' => $tanggal,
            'items' => [['barang_id' => $barang->id, 'stok_fisik' => 18]],
        ])->assertSessionHasNoErrors();
        $this->assertEquals(18, $cek()['stok']);
        $this->assertEquals(54_000, $cek()['persediaan']);

        // 4. Penjualan kredit 6 @ 9.000 + PPN
        $this->post(route('penjualan.store'), [
            'tanggal' => $tanggal, 'customer_id' => $cust->id, 'metode_bayar' => 'kredit',
            'diskon' => 0, 'diskon_tipe' => 'nominal', 'sync_harga' => null, 'pajak_id' => $pajak->id,
            'items' => [['barang_id' => $barang->id, 'jumlah' => 6, 'harga_satuan' => 9000, 'diskon' => 0]],
        ])->assertSessionHasNoErrors();
        $this->assertEquals(12, $cek()['stok']);
        $this->assertEquals(36_000, $cek()['persediaan']);
        $this->assertStokGudangSeimbang($barang, 'stlh penjualan');

        // 5. Transfer gudang 3 -> stok total tetap, bergeser antar gudang
        $this->post(route('transfer-gudang.store'), [
            'tanggal' => $tanggal,
            'gudang_asal' => $gudangUtama->id,
            'gudang_tujuan' => $gudangTujuan->id,
            'keterangan' => 'Ke cabang',
            'items' => [['barang_id' => $barang->id, 'jumlah' => 3]],
        ])->assertSessionHasNoErrors();
        $this->assertEquals(12, $cek()['stok']);
        $this->assertEquals(36_000, $cek()['persediaan']);
        $this->assertEquals(3, (float) StokGudang::where('barang_id', $barang->id)->where('gudang_id', $gudangTujuan->id)->value('qty'));
        $this->assertStokGudangSeimbang($barang, 'stlh transfer');

        // 6. Retur penjualan 2 -> stok kembali, piutang berkurang
        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $penjualanItem = DB::table('penjualan_items')->where('penjualan_id', $penjualan->id)->first();
        $this->post(route('retur-penjualan.store'), [
            'tanggal' => $tanggal, 'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $penjualanItem->id, 'jumlah' => 2]],
        ])->assertSessionHasNoErrors();
        $this->assertEquals(14, $cek()['stok']);
        $this->assertEquals(42_000, $cek()['persediaan']);

        // 7. Retur pembelian 5 -> stok berkurang, hutang berkurang
        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $pembelianItem = DB::table('pembelian_items')->where('pembelian_id', $pembelian->id)->first();
        $this->post(route('retur-pembelian.store'), [
            'tanggal' => $tanggal, 'pembelian_id' => $pembelian->id,
            'items' => [['pembelian_item_id' => $pembelianItem->id, 'jumlah' => 5]],
        ])->assertSessionHasNoErrors();
        $this->assertEquals(9, $cek()['stok']);
        $this->assertEquals(27_000, $cek()['persediaan']);
        $this->assertStokGudangSeimbang($barang, 'stlh retur pembelian');

        // 8. Kas masuk pelunasan piutang 39.960 (59.940 - 19.980)
        $this->post(route('kas-masuk.store'), [
            'tanggal' => $tanggal,
            'rekening_id' => $kas->id,
            'customer_id' => $cust->id,
            'keterangan' => 'Pelunasan piutang',
            'items' => [['akun_id' => $this->akunKode('113'), 'keterangan' => 'Pelunasan', 'nominal' => 39_960]],
        ])->assertSessionHasNoErrors();
        $this->assertEquals(0, (float) BbPiutang::where('customer_id', $cust->id)->orderByDesc('id')->first()->saldo);

        // 9. Mutasi bank 2.500.000 dari bank ke kas
        $this->post(route('mutasi-bank.store'), [
            'tanggal' => $tanggal,
            'rekening_asal_id' => $bank->id,
            'rekening_tujuan_id' => $kas->id,
            'nominal' => 2_500_000,
            'keterangan' => 'Tarik tunai',
        ])->assertSessionHasNoErrors();

        // 10. Kas keluar pelunasan hutang 49.950 (66.600 - 16.650)
        $this->post(route('kas-keluar.store'), [
            'tanggal' => $tanggal,
            'rekening_id' => $kas->id,
            'supplier_id' => $sup->id,
            'keterangan' => 'Lunasi hutang',
            'items' => [['akun_id' => $this->akunKode('211'), 'keterangan' => 'Bayar hutang', 'nominal' => 49_950]],
        ])->assertSessionHasNoErrors();
        $this->assertEquals(0, (float) BbHutang::where('supplier_id', $sup->id)->orderByDesc('id')->first()->saldo);

        // === Invariant akhir ===
        $this->assertEquals(9, (float) $barang->refresh()->stok);
        $this->assertEquals(27_000, (float) JurnalItem::where('akun_id', $akun115)
            ->whereHas('jurnal', fn ($q) => $q->where('is_posted', true))
            ->selectRaw('COALESCE(SUM(debit),0) - COALESCE(SUM(kredit),0) as s')
            ->value('s'));

        $this->assertEqualsWithDelta(2_490_010, $kas->saldo, 0.01, 'Saldo kas');
        $this->assertEqualsWithDelta(2_500_000, $bank->saldo, 0.01, 'Saldo bank');
        $this->assertTrialBalanceSeimbang('akhir');

        $neraca = ReportService::neraca(now()->toDateString());
        $this->assertTrue($neraca['is_balanced']);

        $laba = ReportService::labaRugi(now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString());
        $this->assertEqualsWithDelta(36_000, $laba['total_pendapatan'], 0.01, 'Pendapatan net');
        $this->assertEqualsWithDelta(18_000, $laba['total_beban'], 0.01, 'Beban (HPP + barang rusak)');
        $this->assertEqualsWithDelta(18_000, $laba['laba_rugi'], 0.01, 'Laba');
    }
}
