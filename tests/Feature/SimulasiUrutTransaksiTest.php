<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\Barang;
use App\Models\BbHutang;
use App\Models\BbPersediaan;
use App\Models\BbPiutang;
use App\Models\Customer;
use App\Models\JurnalItem;
use App\Models\Pajak;
use App\Models\Rekening;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SimulasiUrutTransaksiTest extends TestCase
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
            'kode' => 'BRG-URT-'.substr((string) uniqid(), -5),
            'nama' => 'Barang Urut',
            'tipe' => 'barang',
            'stok' => 0,
            'harga_beli' => 3000,
            'harga_jual' => 9000,
            'is_aktif' => true,
        ]);
    }

    private function makeSupplier(): Supplier
    {
        return Supplier::create(['kode' => 'SUP-URT-'.substr((string) uniqid(), -5), 'nama' => 'Supplier Urut', 'is_aktif' => true]);
    }

    private function makeCustomer(): Customer
    {
        return Customer::create(['kode' => 'CUST-URT-'.substr((string) uniqid(), -5), 'nama' => 'Customer Urut', 'is_aktif' => true]);
    }

    private function makePajak(): Pajak
    {
        return Pajak::create(['nama' => 'PPN 11%', 'rate' => 11, 'is_aktif' => true]);
    }

    private function makeRekeningKas(): Rekening
    {
        return Rekening::create([
            'jenis' => 'kas',
            'nama' => 'Kas Urut',
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

    private function beli(int $supplierId, int $barangId, float $qty, float $harga, ?Pajak $pajak = null): void
    {
        $this->post(route('pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $supplierId,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'pajak_id' => $pajak?->id,
            'items' => [
                ['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => $harga, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();
    }

    private function jual(int $customerId, int $barangId, float $qty, float $harga, ?Pajak $pajak = null): void
    {
        $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $customerId,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'pajak_id' => $pajak?->id,
            'items' => [
                ['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => $harga, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();
    }

    private function debet(string $kode): float
    {
        return (float) JurnalItem::where('akun_id', $this->akunKode($kode))->sum('debit');
    }

    private function kredit(string $kode): float
    {
        return (float) JurnalItem::where('akun_id', $this->akunKode($kode))->sum('kredit');
    }

    private function bbTerakhir(int $barangId): BbPersediaan
    {
        return BbPersediaan::where('barang_id', $barangId)->orderByDesc('id')->first();
    }

    // ===== Simulasi Utama =====

    public function test_simulasi_alur_urut_penjualan_pembelian_dan_retur(): void
    {
        $sup = $this->makeSupplier();
        $cust = $this->makeCustomer();
        $brg = $this->makeBarang();
        $brg->refresh();
        $this->assertEquals(0, (float) $brg->stok);
        $this->assertEquals(0, BbPersediaan::count());

        // === Tahap 1: Pembelian 10 @ 3000 (PPN 11%, kredit) ===
        $this->beli($sup->id, $brg->id, 10, 3000, $this->makePajak());

        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $this->assertEquals('posted', $pembelian->status);
        $this->assertEquals(30000, (float) $pembelian->subtotal);
        $this->assertEquals(3300, (float) $pembelian->pajak_nominal);
        $this->assertEquals(33300, (float) $pembelian->total);

        $brg->refresh();
        $this->assertEquals(10, (float) $brg->stok);
        $this->assertEqualsWithDelta(3000, (float) $brg->harga_avg, 0.01);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 30000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('213'), 'debit' => 3300, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('211'), 'debit' => 0, 'kredit' => 33300]);

        $bb = $this->bbTerakhir($brg->id);
        $this->assertEquals(10, (float) $bb->saldo_qty);
        $this->assertEquals(30000, (float) $bb->saldo_harga);
        $this->assertEqualsWithDelta(3000, (float) $bb->ratt, 0.01);

        $hutang = BbHutang::where('supplier_id', $sup->id)->orderByDesc('id')->first();
        $this->assertEqualsWithDelta(33300, (float) $hutang->saldo, 0.01);

        // === Tahap 2: Penjualan 4 @ 9000 (PPN 11%, kredit) ===
        $this->jual($cust->id, $brg->id, 4, 9000, $this->makePajak());

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $this->assertEquals('posted', $penjualan->status);
        $this->assertEquals(36000, (float) $penjualan->subtotal);
        $this->assertEquals(3960, (float) $penjualan->pajak_nominal);
        $this->assertEquals(39960, (float) $penjualan->total);
        $this->assertEquals(12000, (float) $penjualan->hpp_total);

        $brg->refresh();
        $this->assertEquals(6, (float) $brg->stok);
        $this->assertEqualsWithDelta(3000, (float) $brg->harga_avg, 0.01);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('113'), 'debit' => 39960, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('411'), 'debit' => 0, 'kredit' => 36000]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('212'), 'debit' => 0, 'kredit' => 3960]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('51'), 'debit' => 12000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 0, 'kredit' => 12000]);

        $bb = $this->bbTerakhir($brg->id);
        $this->assertEquals(6, (float) $bb->saldo_qty);
        $this->assertEquals(18000, (float) $bb->saldo_harga);
        $this->assertEquals(4, (float) $bb->keluar_qty);
        $this->assertEqualsWithDelta(3000, (float) $bb->keluar_harga, 0.01);

        $piutang = BbPiutang::where('customer_id', $cust->id)->orderByDesc('id')->first();
        $this->assertEqualsWithDelta(39960, (float) $piutang->saldo, 0.01);

        // === Tahap 3: Retur Penjualan 2 unit ===
        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $this->itemPenjualanId((int) $penjualan->id), 'jumlah' => 2]],
        ])->assertSessionHasNoErrors();

        $returJual = DB::table('retur_penjualan')->orderByDesc('id')->first();
        $this->assertEquals('posted', $returJual->status);

        $brg->refresh();
        $this->assertEquals(8, (float) $brg->stok);
        $this->assertEqualsWithDelta(3000, (float) $brg->harga_avg, 0.01);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('411'), 'debit' => 18000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('212'), 'debit' => 1980, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('113'), 'debit' => 0, 'kredit' => 19980]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 6000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('51'), 'debit' => 0, 'kredit' => 6000]);

        $bb = $this->bbTerakhir($brg->id);
        $this->assertEquals(8, (float) $bb->saldo_qty);
        $this->assertEquals(24000, (float) $bb->saldo_harga);

        $piutang = BbPiutang::where('customer_id', $cust->id)->orderByDesc('id')->first();
        $this->assertEqualsWithDelta(19980, (float) $piutang->saldo, 0.01);
        $this->assertEquals(2, BbPiutang::where('customer_id', $cust->id)->count());

        // === Tahap 4: Retur Pembelian 5 unit ===
        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'pembelian_id' => $pembelian->id,
            'items' => [['pembelian_item_id' => $this->itemPembelianId((int) $pembelian->id), 'jumlah' => 5]],
        ])->assertSessionHasNoErrors();

        $returBeli = DB::table('retur_pembelian')->orderByDesc('id')->first();
        $this->assertEquals('posted', $returBeli->status);

        $brg->refresh();
        $this->assertEquals(3, (float) $brg->stok);
        $this->assertEqualsWithDelta(3000, (float) $brg->harga_avg, 0.01);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 0, 'kredit' => 15000]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('213'), 'debit' => 0, 'kredit' => 1650]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('211'), 'debit' => 16650, 'kredit' => 0]);

        $bb = $this->bbTerakhir($brg->id);
        $this->assertEquals(3, (float) $bb->saldo_qty);
        $this->assertEquals(9000, (float) $bb->saldo_harga);
        $this->assertEquals(4, BbPersediaan::where('barang_id', $brg->id)->count());

        $hutang = BbHutang::where('supplier_id', $sup->id)->orderByDesc('id')->first();
        $this->assertEqualsWithDelta(16650, (float) $hutang->saldo, 0.01);
        $this->assertEquals(2, BbHutang::where('supplier_id', $sup->id)->count());

        // === Posisi akhir: BB persediaan konsisten dengan stok & ratt ===
        $this->assertEquals(9000, $this->debet('115') - $this->kredit('115'));
        $this->assertEquals(6000, $this->debet('51') - $this->kredit('51'));
        $this->assertEquals(18000, $this->kredit('411') - $this->debet('411'));
        $this->assertEquals(1980, $this->kredit('212') - $this->debet('212'));
        $this->assertEquals(1650, $this->debet('213') - $this->kredit('213'));
        $this->assertEquals(19980, $this->debet('113') - $this->kredit('113'));
        $this->assertEquals(16650, $this->kredit('211') - $this->debet('211'));

        // Transaksi asli tetap posted (ditandai retur), berikutnya bisa diretur ulang
        $this->assertEquals('posted', DB::table('penjualans')->where('id', $penjualan->id)->value('status'));
        $this->assertEquals('posted', DB::table('pembelians')->where('id', $pembelian->id)->value('status'));
    }

    public function test_penjualan_dengan_retur_aktif_tidak_bisa_divoid(): void
    {
        $sup = $this->makeSupplier();
        $cust = $this->makeCustomer();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);
        $this->jual($cust->id, $brg->id, 4, 9000);

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $this->itemPenjualanId((int) $penjualan->id), 'jumlah' => 2]],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $bbSebelum = BbPersediaan::where('barang_id', $brg->id)->count();

        // Void penjualan DITOLAK karena masih ada retur posted
        $this->post(route('penjualan.void', $penjualan->id))->assertSessionHas('error');

        $this->assertEquals('posted', DB::table('penjualans')->where('id', $penjualan->id)->value('status'));
        $brg->refresh();
        $this->assertEquals(8, (float) $brg->stok);
        $this->assertEquals($bbSebelum, BbPersediaan::where('barang_id', $brg->id)->count());
    }

    public function test_pembelian_dengan_retur_aktif_tidak_bisa_divoid(): void
    {
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);

        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'pembelian_id' => $pembelian->id,
            'items' => [['pembelian_item_id' => $this->itemPembelianId((int) $pembelian->id), 'jumlah' => 4]],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $bbSebelum = BbPersediaan::where('barang_id', $brg->id)->count();

        $this->post(route('pembelian.void', $pembelian->id))->assertSessionHas('error');

        $this->assertEquals('posted', DB::table('pembelians')->where('id', $pembelian->id)->value('status'));
        $brg->refresh();
        $this->assertEquals(6, (float) $brg->stok);
        $this->assertEquals($bbSebelum, BbPersediaan::where('barang_id', $brg->id)->count());
    }

    public function test_retur_menolak_transaksi_sumber_yang_sudah_dibatal(): void
    {
        $sup = $this->makeSupplier();
        $cust = $this->makeCustomer();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);
        $this->jual($cust->id, $brg->id, 4, 9000);

        // Void penjualan (tanpa retur) berhasil -> draft
        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $this->post(route('penjualan.void', $penjualan->id))->assertSessionHasNoErrors();
        $this->assertEquals('draft', DB::table('penjualans')->where('id', $penjualan->id)->value('status'));

        // Retur atas penjualan draft ditolak
        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $this->itemPenjualanId((int) $penjualan->id), 'jumlah' => 2]],
        ])->assertSessionHas('error');
        $this->assertEquals(0, DB::table('retur_penjualan')->count());

        // Void pembelian (tanpa retur) berhasil -> draft
        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $this->post(route('pembelian.void', $pembelian->id))->assertSessionHasNoErrors();
        $this->assertEquals('draft', DB::table('pembelians')->where('id', $pembelian->id)->value('status'));

        // Retur atas pembelian draft ditolak
        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'pembelian_id' => $pembelian->id,
            'items' => [['pembelian_item_id' => $this->itemPembelianId((int) $pembelian->id), 'jumlah' => 2]],
        ])->assertSessionHas('error');
        $this->assertEquals(0, DB::table('retur_pembelian')->count());

        $brg->refresh();
        $this->assertEquals(0, (float) $brg->stok);
    }

    private function itemPenjualanId(int $penjualanId): int
    {
        return (int) DB::table('penjualan_items')->where('penjualan_id', $penjualanId)->value('id');
    }

    private function itemPembelianId(int $pembelianId): int
    {
        return (int) DB::table('pembelian_items')->where('pembelian_id', $pembelianId)->value('id');
    }
}
