<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\Barang;
use App\Models\BbHutang;
use App\Models\BbPiutang;
use App\Models\Customer;
use App\Models\Pajak;
use App\Models\Rekening;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SimulasiReturTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::where('email', 'admin@keuangan.test')->first());
    }

    // ===== Helper =====

    private function makeBarang(): Barang
    {
        return Barang::create([
            'kode' => 'BRG-RET-'.substr((string) uniqid(), -5),
            'nama' => 'Barang Retur',
            'tipe' => 'barang',
            'stok' => 0,
            'harga_beli' => 3000,
            'harga_jual' => 9000,
            'is_aktif' => true,
        ]);
    }

    private function makeSupplier(): Supplier
    {
        return Supplier::create(['kode' => 'SUP-RET-'.substr((string) uniqid(), -5), 'nama' => 'Supplier Retur', 'is_aktif' => true]);
    }

    private function makeCustomer(): Customer
    {
        return Customer::create(['kode' => 'CUST-RET-'.substr((string) uniqid(), -5), 'nama' => 'Customer Retur', 'is_aktif' => true]);
    }

    private function makePajak(): Pajak
    {
        return Pajak::create(['nama' => 'PPN 11%', 'rate' => 11, 'is_aktif' => true]);
    }

    private function makeRekeningKas(): Rekening
    {
        return Rekening::create([
            'jenis' => 'kas',
            'nama' => 'Kas Retur',
            'nomor_rekening' => null,
            'nama_pemilik' => null,
            'akun_id' => $this->akunKode('111'),
            'saldo_awal' => 0,
            'is_aktif' => true,
        ]);
    }

    private function akunKode(string $kode): int
    {
        return (int) AkunPerkiraan::where('kode', $kode)->value('id');
    }

    private function beli(int $supplierId, int $barangId, float $qty, float $harga, float $diskonItem = 0.0, float $diskonGlobal = 0.0, ?Pajak $pajak = null, string $metode = 'kredit', ?int $rekeningId = null): void
    {
        $this->post(route('pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $supplierId,
            'metode_bayar' => $metode,
            'rekening_id' => $rekeningId,
            'diskon' => $diskonGlobal,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'pajak_id' => $pajak?->id,
            'items' => [
                ['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => $harga, 'diskon' => $diskonItem],
            ],
        ])->assertSessionHasNoErrors();
    }

    private function jual(int $customerId, int $barangId, float $qty, float $harga, float $diskonItem = 0.0, float $diskonGlobal = 0.0, ?Pajak $pajak = null, string $metode = 'kredit', ?int $rekeningId = null): void
    {
        $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $customerId,
            'metode_bayar' => $metode,
            'rekening_id' => $rekeningId,
            'diskon' => $diskonGlobal,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'pajak_id' => $pajak?->id,
            'items' => [
                ['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => $harga, 'diskon' => $diskonItem],
            ],
        ])->assertSessionHasNoErrors();
    }

    private function itemPenjualanId(int $penjualanId): int
    {
        return (int) DB::table('penjualan_items')->where('penjualan_id', $penjualanId)->value('id');
    }

    private function itemPembelianId(int $pembelianId): int
    {
        return (int) DB::table('pembelian_items')->where('pembelian_id', $pembelianId)->value('id');
    }

    // ===== Retur Penjualan =====

    public function test_retur_penjualan_tanpa_diskon_dan_pajak(): void
    {
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $cust = $this->makeCustomer();
        $this->beli($sup->id, $brg->id, 10, 3000);
        $this->jual($cust->id, $brg->id, 4, 9000);
        $brg->refresh();
        $this->assertEquals(6, (float) $brg->stok);

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $this->itemPenjualanId($penjualan->id), 'jumlah' => 2]],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(8, (float) $brg->stok);

        $returItem = DB::table('retur_penjualan_items')->orderByDesc('id')->first();
        $this->assertEquals(9000, (float) $returItem->harga_satuan);
        $this->assertEquals(18000, (float) $returItem->subtotal);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('411'), 'debit' => 18000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('113'), 'debit' => 0, 'kredit' => 18000]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 6000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('51'), 'debit' => 0, 'kredit' => 6000]);
    }

    public function test_retur_penjualan_menggunakan_neto_saat_ada_diskon_item_dan_pajak(): void
    {
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $cust = $this->makeCustomer();
        $this->beli($sup->id, $brg->id, 10, 3000);
        $this->jual($cust->id, $brg->id, 4, 9000, diskonItem: 2000, pajak: $this->makePajak());

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $this->assertEquals(34000, (float) $penjualan->subtotal);
        $this->assertEquals(3740, (float) $penjualan->pajak_nominal);

        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $this->itemPenjualanId($penjualan->id), 'jumlah' => 2]],
        ])->assertSessionHasNoErrors();

        $returItem = DB::table('retur_penjualan_items')->orderByDesc('id')->first();
        $this->assertEquals(8500, (float) $returItem->harga_satuan);
        $this->assertEquals(17000, (float) $returItem->subtotal);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('411'), 'debit' => 17000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('212'), 'debit' => 1870, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('113'), 'debit' => 0, 'kredit' => 18870]);
    }

    public function test_retur_penjualan_dengan_diskon_global_dan_pajak(): void
    {
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $cust = $this->makeCustomer();
        $this->beli($sup->id, $brg->id, 10, 3000);
        $this->jual($cust->id, $brg->id, 4, 9000, diskonGlobal: 4000, pajak: $this->makePajak());

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $this->assertEquals(36000, (float) $penjualan->subtotal);
        $this->assertEquals(4000, (float) $penjualan->diskon_nominal);
        $this->assertEquals(3520, (float) $penjualan->pajak_nominal);

        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $this->itemPenjualanId($penjualan->id), 'jumlah' => 2]],
        ])->assertSessionHasNoErrors();

        $returItem = DB::table('retur_penjualan_items')->orderByDesc('id')->first();
        $this->assertEquals(8000, (float) $returItem->harga_satuan);
        $this->assertEquals(16000, (float) $returItem->subtotal);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('411'), 'debit' => 16000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('212'), 'debit' => 1760, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('113'), 'debit' => 0, 'kredit' => 17760]);
    }

    public function test_retur_penjualan_metode_tunai_mengembalikan_ke_akun_rekening(): void
    {
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $cust = $this->makeCustomer();
        $rek = $this->makeRekeningKas();
        $this->beli($sup->id, $brg->id, 10, 3000);
        $this->jual($cust->id, $brg->id, 4, 9000, metode: 'tunai', rekeningId: $rek->id);

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $this->itemPenjualanId($penjualan->id), 'jumlah' => 2]],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(8, (float) $brg->stok);

        // Balik ke rekening (akun kas/bank), bukan ke piutang
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('411'), 'debit' => 18000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('111'), 'debit' => 0, 'kredit' => 18000]);
        $this->assertDatabaseMissing('jurnal_items', ['akun_id' => $this->akunKode('113'), 'credit' => 18000]);
        $this->assertEquals(0, BbPiutang::where('customer_id', $cust->id)->count());
    }

    public function test_retur_penjualan_bertahap_dua_kali(): void
    {
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $cust = $this->makeCustomer();
        $this->beli($sup->id, $brg->id, 10, 3000);
        $this->jual($cust->id, $brg->id, 4, 9000);

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $itemId = $this->itemPenjualanId($penjualan->id);

        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(), 'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $itemId, 'jumlah' => 1]],
        ])->assertSessionHasNoErrors();
        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(), 'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $itemId, 'jumlah' => 1]],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(8, (float) $brg->stok);
        $this->assertEquals(2, DB::table('retur_penjualan')->where('status', 'posted')->count());
        $this->assertEquals(2, (float) DB::table('retur_penjualan_items')->sum('jumlah'));
        $this->assertEquals(3, BbPiutang::where('customer_id', $cust->id)->count());
    }

    public function test_retur_penjualan_void_mengembalikan_stok_dan_piutang_lengkap(): void
    {
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $cust = $this->makeCustomer();
        $this->beli($sup->id, $brg->id, 10, 3000);
        $this->jual($cust->id, $brg->id, 4, 9000, pajak: $this->makePajak());

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $this->itemPenjualanId($penjualan->id), 'jumlah' => 2]],
        ])->assertSessionHasNoErrors();
        $brg->refresh();
        $this->assertEquals(8, (float) $brg->stok);

        $retur = DB::table('retur_penjualan')->orderByDesc('id')->first();
        $this->post(route('retur-penjualan.void', $retur->id))->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(6, (float) $brg->stok);
        $this->assertEquals('draft', DB::table('retur_penjualan')->where('id', $retur->id)->value('status'));

        // BB piutang dibalik penuh (neto + pajak) = 19980
        $last = BbPiutang::where('customer_id', $cust->id)->orderByDesc('id')->first();
        $this->assertEqualsWithDelta(19980, (float) $last->debit, 0.01);
        $this->assertEquals(0, (float) $last->kredit);
    }

    public function test_retur_penjualan_menolak_jumlah_melebihi_sisa(): void
    {
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $cust = $this->makeCustomer();
        $this->beli($sup->id, $brg->id, 10, 3000);
        $this->jual($cust->id, $brg->id, 4, 9000);
        $brg->refresh();
        $this->assertEquals(6, (float) $brg->stok);

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $this->itemPenjualanId($penjualan->id), 'jumlah' => 5]],
        ])->assertSessionHas('error');

        $brg->refresh();
        $this->assertEquals(6, (float) $brg->stok);
        $this->assertEquals(0, DB::table('retur_penjualan')->where('status', 'posted')->count());
    }

    // ===== Retur Pembelian =====

    public function test_retur_pembelian_tanpa_diskon_dan_pajak(): void
    {
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);
        $brg->refresh();
        $this->assertEquals(10, (float) $brg->stok);

        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'pembelian_id' => $pembelian->id,
            'items' => [['pembelian_item_id' => $this->itemPembelianId($pembelian->id), 'jumlah' => 4]],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(6, (float) $brg->stok);

        $returItem = DB::table('retur_pembelian_items')->orderByDesc('id')->first();
        $this->assertEquals(3000, (float) $returItem->harga_satuan);
        $this->assertEquals(12000, (float) $returItem->subtotal);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 0, 'kredit' => 12000]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('211'), 'debit' => 12000, 'kredit' => 0]);
    }

    public function test_retur_pembelian_menggunakan_harga_net_dan_membalik_ppn_masukan(): void
    {
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000, diskonItem: 2000, diskonGlobal: 1000, pajak: $this->makePajak());

        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $pembelianItem = DB::table('pembelian_items')->where('pembelian_id', $pembelian->id)->first();
        $this->assertEquals(28000, (float) $pembelian->subtotal);
        $this->assertEquals(1000, (float) $pembelian->diskon_nominal);
        $this->assertEquals(2970, (float) $pembelian->pajak_nominal);
        $this->assertEquals(2700, (float) $pembelianItem->harga_net);

        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'pembelian_id' => $pembelian->id,
            'items' => [['pembelian_item_id' => $this->itemPembelianId($pembelian->id), 'jumlah' => 4]],
        ])->assertSessionHasNoErrors();

        $returItem = DB::table('retur_pembelian_items')->orderByDesc('id')->first();
        $this->assertEquals(2700, (float) $returItem->harga_satuan);
        $this->assertEquals(10800, (float) $returItem->subtotal);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 0, 'kredit' => 10800]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('213'), 'debit' => 0, 'kredit' => 1188]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('211'), 'debit' => 11988, 'kredit' => 0]);
    }

    public function test_retur_pembelian_metode_tunai_mengembalikan_dari_akun_rekening(): void
    {
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $rek = $this->makeRekeningKas();
        $this->beli($sup->id, $brg->id, 4, 3000, metode: 'tunai', rekeningId: $rek->id);
        $brg->refresh();
        $this->assertEquals(4, (float) $brg->stok);

        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'pembelian_id' => $pembelian->id,
            'items' => [['pembelian_item_id' => $this->itemPembelianId($pembelian->id), 'jumlah' => 2]],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(2, (float) $brg->stok);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 0, 'kredit' => 6000]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('111'), 'debit' => 6000, 'kredit' => 0]);
        $this->assertEquals(0, BbHutang::where('supplier_id', $sup->id)->count());
    }

    public function test_retur_pembelian_bertahap_dua_kali(): void
    {
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);

        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $itemId = $this->itemPembelianId($pembelian->id);

        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(), 'pembelian_id' => $pembelian->id,
            'items' => [['pembelian_item_id' => $itemId, 'jumlah' => 3]],
        ])->assertSessionHasNoErrors();
        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(), 'pembelian_id' => $pembelian->id,
            'items' => [['pembelian_item_id' => $itemId, 'jumlah' => 2]],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(5, (float) $brg->stok);
        $this->assertEquals(2, DB::table('retur_pembelian')->where('status', 'posted')->count());
        $this->assertEquals(5, (float) DB::table('retur_pembelian_items')->sum('jumlah'));
        $this->assertEquals(3, BbHutang::where('supplier_id', $sup->id)->count());
    }

    public function test_retur_pembelian_void_mengembalikan_stok_dan_hutang_lengkap(): void
    {
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000, pajak: $this->makePajak());

        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'pembelian_id' => $pembelian->id,
            'items' => [['pembelian_item_id' => $this->itemPembelianId($pembelian->id), 'jumlah' => 4]],
        ])->assertSessionHasNoErrors();
        $brg->refresh();
        $this->assertEquals(6, (float) $brg->stok);

        $retur = DB::table('retur_pembelian')->orderByDesc('id')->first();
        $this->post(route('retur-pembelian.void', $retur->id))->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(10, (float) $brg->stok);
        $this->assertEquals('draft', DB::table('retur_pembelian')->where('id', $retur->id)->value('status'));

        // BB hutang dibalik penuh (neto + pajak) = 13320
        $last = BbHutang::where('supplier_id', $sup->id)->orderByDesc('id')->first();
        $this->assertEqualsWithDelta(13320, (float) $last->kredit, 0.01);
        $this->assertEquals(0, (float) $last->debit);
    }

    public function test_retur_pembelian_menolak_jumlah_melebihi_sisa(): void
    {
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);

        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'pembelian_id' => $pembelian->id,
            'items' => [['pembelian_item_id' => $this->itemPembelianId($pembelian->id), 'jumlah' => 12]],
        ])->assertSessionHas('error');

        $brg->refresh();
        $this->assertEquals(10, (float) $brg->stok);
        $this->assertEquals(0, DB::table('retur_pembelian')->where('status', 'posted')->count());
    }

    public function test_form_retur_memuat_daftar_transaksi_yang_bisa_diretur(): void
    {
        $sup = $this->makeSupplier();
        $cust = $this->makeCustomer();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);
        $this->jual($cust->id, $brg->id, 4, 9000);

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();

        // Form memuat daftar transaksi yang masih bisa diretur (sumber dropdown)
        $this->get(route('retur-penjualan.create'))->assertOk()->assertSee($penjualan->nomor, false);
        $this->get(route('retur-pembelian.create'))->assertOk()->assertSee($pembelian->nomor, false);

        // Setelah seluruh barang diretur, transaksi tidak lagi muncul di dropdown
        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $this->itemPenjualanId((int) $penjualan->id), 'jumlah' => 4]],
        ])->assertSessionHasNoErrors();
        $this->get(route('retur-penjualan.create'))->assertOk()->assertDontSee($penjualan->nomor, false);

        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'pembelian_id' => $pembelian->id,
            'items' => [['pembelian_item_id' => $this->itemPembelianId((int) $pembelian->id), 'jumlah' => 10]],
        ])->assertSessionHasNoErrors();
        $this->get(route('retur-pembelian.create'))->assertOk()->assertDontSee($pembelian->nomor, false);
    }

    public function test_form_retur_memakai_url_sumber_relatif(): void
    {
        // URL sumber-items harus relatif (same-origin) agar tidak gagal saat
        // user membuka aplikasi lewat host berbeda dari APP_URL (mis. 127.0.0.1 vs localhost).
        foreach (['retur-penjualan', 'retur-pembelian'] as $prefix) {
            $html = str_replace('\\/', '/', $this->get(route($prefix.'.create'))->assertOk()->getContent());
            $this->assertStringContainsString('/'.$prefix.'/sumber/__ID__/items', $html);
            $this->assertStringNotContainsString(rtrim(url('/'), '/').'/'.$prefix.'/sumber', $html);
        }
    }

    public function test_transaksi_dengan_semua_barang_diretur_tidak_lagi_muncul_di_pencarian(): void
    {
        $sup = $this->makeSupplier();
        $cust = $this->makeCustomer();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);
        $this->jual($cust->id, $brg->id, 2, 9000);

        $this->assertCount(1, $this->getJson(route('retur-penjualan.search'))->assertOk()->json());
        $this->assertCount(1, $this->getJson(route('retur-pembelian.search'))->assertOk()->json());

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(), 'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $this->itemPenjualanId($penjualan->id), 'jumlah' => 2]],
        ])->assertSessionHasNoErrors();
        $this->getJson(route('retur-penjualan.search'))->assertOk()->assertExactJson([]);

        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(), 'pembelian_id' => $pembelian->id,
            'items' => [['pembelian_item_id' => $this->itemPembelianId($pembelian->id), 'jumlah' => 10]],
        ])->assertSessionHasNoErrors();
        $this->getJson(route('retur-pembelian.search'))->assertOk()->assertExactJson([]);
    }

    public function test_halaman_index_transaksi_menampilkan_badge_jumlah_retur(): void
    {
        $sup = $this->makeSupplier();
        $cust = $this->makeCustomer();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);
        $this->jual($cust->id, $brg->id, 4, 9000);

        $this->get(route('penjualan.index'))->assertOk()->assertDontSee('Diretur');
        $this->get(route('pembelian.index'))->assertOk()->assertDontSee('Diretur');

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(), 'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $this->itemPenjualanId($penjualan->id), 'jumlah' => 1]],
        ])->assertSessionHasNoErrors();

        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(), 'pembelian_id' => $pembelian->id,
            'items' => [['pembelian_item_id' => $this->itemPembelianId($pembelian->id), 'jumlah' => 2]],
        ])->assertSessionHasNoErrors();

        $this->get(route('penjualan.index'))->assertOk()->assertSee('Diretur');
        $this->get(route('pembelian.index'))->assertOk()->assertSee('Diretur');
    }
}
