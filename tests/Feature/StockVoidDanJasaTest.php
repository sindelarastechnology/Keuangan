<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\Barang;
use App\Models\BbPersediaan;
use App\Models\BbPiutang;
use App\Models\Customer;
use App\Models\JurnalItem;
use App\Models\Penjualan;
use App\Models\Rekening;
use App\Models\Supplier;
use App\Models\User;
use App\Services\PelunasanService;
use App\Services\PiutangAttributionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class StockVoidDanJasaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function login(): void
    {
        $this->actingAs(User::where('email', 'admin@keuangan.test')->first());
    }

    private function makeBarang(): array
    {
        $brg = Barang::create([
            'kode' => 'BRG-'.substr((string) uniqid(), -5),
            'nama' => 'Barang Void',
            'tipe' => 'barang',
            'stok' => 0,
            'harga_beli' => 3000,
            'harga_jual' => 8000,
            'is_aktif' => true,
        ]);
        $sup = Supplier::create(['kode' => 'SUP-'.substr((string) uniqid(), -5), 'nama' => 'Supplier Void', 'is_aktif' => true]);

        return [$brg, $sup];
    }

    private function postPembelian(int $supplierId, int $barangId, float $qty, float $harga, string $metode = 'kredit'): TestResponse
    {
        return $this->post(route('pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $supplierId,
            'metode_bayar' => $metode,
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [
                ['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => $harga, 'diskon' => 0],
            ],
        ]);
    }

    public function test_void_pembelian_mengembalikan_stok_dan_rata_rata_harga(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang();

        // Beli 5 @ 3000 -> stok 5 avg 3000
        $this->postPembelian($sup->id, $brg->id, 5, 3000)->assertSessionHasNoErrors();

        // Beli 5 @ 4200 (sync off) -> stok 10 avg 3600
        $pb2 = DB::table('pembelians')->orderByDesc('id')->first();
        $this->postPembelian($sup->id, $brg->id, 5, 4200)->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(10, (float) $brg->stok);
        $this->assertEqualsWithDelta(3600, (float) $brg->harga_avg, 0.01);

        // Void pembelian kedua -> stok 5 avg 3000 kembali
        $pbToVoid = DB::table('pembelians')->where('id', '>', $pb2->id)->orderBy('id')->first();
        $this->post(route('pembelian.void', $pbToVoid->id))
            ->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(5, (float) $brg->stok);
        $this->assertEqualsWithDelta(3000, (float) $brg->harga_avg, 0.01);

        // BB persediaan terakhir harus konsisten dengan stok & rata-rata
        $last = BbPersediaan::where('barang_id', $brg->id)->orderByDesc('id')->first();
        $this->assertEquals(5, (float) $last->saldo_qty);
        $this->assertEqualsWithDelta(3000, (float) $last->ratt, 0.01);
        $this->assertEqualsWithDelta(15000, (float) $last->saldo_harga, 0.01);
    }

    public function test_void_penjualan_mengembalikan_stok_dan_menjaga_rata_rata(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang();

        // Bangun stok: 10 unit avg 3000
        $this->postPembelian($sup->id, $brg->id, 10, 3000)->assertSessionHasNoErrors();

        $cust = Customer::create(['kode' => 'CUST-'.substr((string) uniqid(), -5), 'nama' => 'Customer Void', 'is_aktif' => true]);

        $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $cust->id,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 4, 'harga_satuan' => 9000, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(6, (float) $brg->stok);
        $this->assertEqualsWithDelta(3000, (float) $brg->harga_avg, 0.01);

        // Void penjualan -> stok kembali 10, rata-rata tetap
        $pj = DB::table('penjualans')->orderByDesc('id')->first();
        $this->post(route('penjualan.void', $pj->id))->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(10, (float) $brg->stok);
        $this->assertEqualsWithDelta(3000, (float) $brg->harga_avg, 0.01);
    }

    public function test_void_penjualan_kredit_yang_sudah_dilunasi_ditolak(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang();

        // Bangun stok: 10 unit avg 3000
        $this->postPembelian($sup->id, $brg->id, 10, 3000)->assertSessionHasNoErrors();

        $cust = Customer::create(['kode' => 'CUST-'.substr((string) uniqid(), -5), 'nama' => 'Customer Void Piutang', 'is_aktif' => true]);

        $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $cust->id,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 4, 'harga_satuan' => 9000, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        $pj = DB::table('penjualans')->orderByDesc('id')->first();
        $total = (float) $pj->total;

        // Baris piutang awal (debit) tercatat dengan saldo = total
        $this->assertEqualsWithDelta($total, (float) BbPiutang::where('customer_id', $cust->id)->sum(DB::raw('debit - kredit')), 0.01);

        // Sebagian piutang dilunasi -> saldo customer berkurang sebesar nominal
        $rekening = Rekening::factory()->create();
        PelunasanService::bayarPiutang($cust, 20000, $rekening->id, now()->toDateString(), 'Bayar sebagian');

        $this->assertEqualsWithDelta($total - 20000, (float) BbPiutang::where('customer_id', $cust->id)->orderByDesc('id')->value('saldo'), 0.01);
        $penjualan = Penjualan::findOrFail($pj->id);
        $this->assertEqualsWithDelta($total - 20000, PiutangAttributionService::sisa($penjualan), 0.01);

        // Void penjualan DITOLAK karena sudah ada pelunasan (Kas Masuk).
        // Pembatalan penuh dilakukan dengan membatalkan kas masuk pelunasan terlebih dahulu.
        $this->post(route('penjualan.void', $pj->id))->assertSessionHas('error');

        $this->assertEquals('posted', $penjualan->fresh()->status);
        $this->assertEqualsWithDelta($total - 20000, (float) BbPiutang::where('customer_id', $cust->id)->orderByDesc('id')->value('saldo'), 0.01);
        $this->assertEqualsWithDelta($total - 20000, PiutangAttributionService::sisa($penjualan->fresh()), 0.01);
    }

    public function test_penjualan_jasa_dicatat_ke_akun_412_bukan_411(): void
    {
        $this->login();
        $cust = Customer::create(['kode' => 'CUST-'.substr((string) uniqid(), -5), 'nama' => 'Customer Jasa', 'is_aktif' => true]);

        $jasa = Barang::create([
            'kode' => 'JAS-'.substr((string) uniqid(), -5),
            'nama' => 'Jasa Instalasi',
            'tipe' => 'jasa',
            'stok' => 0,
            'harga_beli' => 0,
            'harga_jual' => 150000,
            'is_aktif' => true,
        ]);
        $barang = Barang::create([
            'kode' => 'BRG-'.substr((string) uniqid(), -5),
            'nama' => 'Barang Campur',
            'tipe' => 'barang',
            'stok' => 50,
            'harga_beli' => 30000,
            'harga_jual' => 60000,
            'harga_avg' => 30000,
            'is_aktif' => true,
        ]);

        $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $cust->id,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [
                ['barang_id' => $barang->id, 'jumlah' => 2, 'harga_satuan' => 60000, 'diskon' => 0],
                ['barang_id' => $jasa->id, 'jumlah' => 1, 'harga_satuan' => 150000, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        $akun411 = AkunPerkiraan::where('kode', '411')->value('id');
        $akun412 = AkunPerkiraan::where('kode', '412')->value('id');

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $akun411, 'debit' => 0, 'kredit' => 120000]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $akun412, 'debit' => 0, 'kredit' => 150000]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $akun412]);

        // Total jurnal penjualan tetap seimbang
        $jurnalId = DB::table('jurnal_umum')->where('tipe', 'penjualan')->where('ref_id', $penjualan->id)->orderByDesc('id')->value('id');
        $totalDebit = (float) JurnalItem::where('jurnal_id', $jurnalId)->sum('debit');
        $totalKredit = (float) JurnalItem::where('jurnal_id', $jurnalId)->sum('kredit');
        $this->assertEqualsWithDelta($totalDebit, $totalKredit, 0.01);

        // Total penjualan = 270000
        $this->assertEqualsWithDelta(270000, (float) $penjualan->total, 0.01);
    }
}
