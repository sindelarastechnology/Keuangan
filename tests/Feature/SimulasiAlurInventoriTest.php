<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\Barang;
use App\Models\BbHutang;
use App\Models\BbPiutang;
use App\Models\Customer;
use App\Models\Pajak;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SimulasiAlurInventoriTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::where('email', 'admin@keuangan.test')->first());
    }

    private function makeBarang(): Barang
    {
        return Barang::create([
            'kode' => 'BRG-SIM-'.substr((string) uniqid(), -5),
            'nama' => 'Barang Simulasi',
            'tipe' => 'barang',
            'stok' => 0,
            'harga_beli' => 3000,
            'harga_jual' => 9000,
            'is_aktif' => true,
        ]);
    }

    private function makeSupplier(): Supplier
    {
        return Supplier::create(['kode' => 'SUP-SIM-'.substr((string) uniqid(), -5), 'nama' => 'Supplier Simulasi', 'is_aktif' => true]);
    }

    private function makeCustomer(): Customer
    {
        return Customer::create(['kode' => 'CUST-SIM-'.substr((string) uniqid(), -5), 'nama' => 'Customer Simulasi', 'is_aktif' => true]);
    }

    private function akunKode(string $kode): int
    {
        return (int) AkunPerkiraan::where('kode', $kode)->value('id');
    }

    public function test_alur_lengkap_empat_fitur_inventori_berjalan_bersamaan(): void
    {
        $sup = $this->makeSupplier();
        $barang = $this->makeBarang();
        $cust = $this->makeCustomer();
        $pajak = Pajak::create(['nama' => 'PPN 11%', 'rate' => 11, 'is_aktif' => true]);
        $tanggal = now()->toDateString();

        // 1. Pembelian kredit 20 @ 3000 + PPN 11% -> stok 20, avg 3000
        $this->post(route('pembelian.store'), [
            'tanggal' => $tanggal,
            'supplier_id' => $sup->id,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'pajak_id' => $pajak->id,
            'items' => [
                ['barang_id' => $barang->id, 'jumlah' => 20, 'harga_satuan' => 3000, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        $barang->refresh();
        $this->assertEquals(20, (float) $barang->stok);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 60000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('213'), 'debit' => 6600, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('211'), 'debit' => 0, 'kredit' => 66600]);

        // 2. Perubahan stok keluar (rusak) 2 -> stok 18
        $this->post(route('perubahan-stok.store'), [
            'tanggal' => $tanggal,
            'jenis' => 'rusak',
            'items' => [
                ['barang_id' => $barang->id, 'arah' => 'keluar', 'jumlah' => 2],
            ],
        ])->assertSessionHasNoErrors();
        $barang->refresh();
        $this->assertEquals(18, (float) $barang->stok);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('531'), 'debit' => 6000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 0, 'kredit' => 6000]);

        // 3. Stok opname selisih lebih (fisik 22) -> stok 22
        $this->post(route('stok-opname.store'), [
            'tanggal' => $tanggal,
            'items' => [
                ['barang_id' => $barang->id, 'stok_fisik' => 22],
            ],
        ])->assertSessionHasNoErrors();
        $barang->refresh();
        $this->assertEquals(22, (float) $barang->stok);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 12000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('421'), 'debit' => 0, 'kredit' => 12000]);

        // 4. Penjualan kredit 6 @ 9000 + PPN 11% -> stok 16
        $this->post(route('penjualan.store'), [
            'tanggal' => $tanggal,
            'customer_id' => $cust->id,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'pajak_id' => $pajak->id,
            'items' => [
                ['barang_id' => $barang->id, 'jumlah' => 6, 'harga_satuan' => 9000, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();
        $barang->refresh();
        $this->assertEquals(16, (float) $barang->stok);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('113'), 'debit' => 59940, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('411'), 'debit' => 0, 'kredit' => 54000]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('212'), 'debit' => 0, 'kredit' => 5940]);

        // 5. Retur penjualan 2 pcs -> stok 18, balik neto + PPN
        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $penjualanItem = DB::table('penjualan_items')->where('penjualan_id', $penjualan->id)->first();
        $this->post(route('retur-penjualan.store'), [
            'tanggal' => $tanggal,
            'penjualan_id' => $penjualan->id,
            'items' => [
                ['penjualan_item_id' => $penjualanItem->id, 'jumlah' => 2],
            ],
        ])->assertSessionHasNoErrors();
        $barang->refresh();
        $this->assertEquals(18, (float) $barang->stok);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('411'), 'debit' => 18000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('212'), 'debit' => 1980, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('113'), 'debit' => 0, 'kredit' => 19980]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 6000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('51'), 'debit' => 0, 'kredit' => 6000]);

        $lastPiutang = BbPiutang::where('customer_id', $cust->id)->orderByDesc('id')->first();
        $this->assertEqualsWithDelta(19980, (float) $lastPiutang->kredit, 0.01);

        // 6. Retur pembelian 5 pcs -> stok 13, balik neto + PPN Masukan
        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $pembelianItem = DB::table('pembelian_items')->where('pembelian_id', $pembelian->id)->first();
        $this->post(route('retur-pembelian.store'), [
            'tanggal' => $tanggal,
            'pembelian_id' => $pembelian->id,
            'items' => [
                ['pembelian_item_id' => $pembelianItem->id, 'jumlah' => 5],
            ],
        ])->assertSessionHasNoErrors();
        $barang->refresh();
        $this->assertEquals(13, (float) $barang->stok);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 0, 'kredit' => 15000]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('213'), 'debit' => 0, 'kredit' => 1650]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('211'), 'debit' => 16650, 'kredit' => 0]);

        $lastHutang = BbHutang::where('supplier_id', $sup->id)->orderByDesc('id')->first();
        $this->assertEqualsWithDelta(16650, (float) $lastHutang->debit, 0.01);

        // 7. Void retur pembelian -> stok 18 lagi
        $returPembelian = DB::table('retur_pembelian')->orderByDesc('id')->first();
        $this->post(route('retur-pembelian.void', $returPembelian->id))->assertSessionHasNoErrors();
        $barang->refresh();
        $this->assertEquals(18, (float) $barang->stok);
        $this->assertEquals('draft', DB::table('retur_pembelian')->where('id', $returPembelian->id)->value('status'));

        // 8. Void retur penjualan -> stok 16 lagi
        $returPenjualan = DB::table('retur_penjualan')->orderByDesc('id')->first();
        $this->post(route('retur-penjualan.void', $returPenjualan->id))->assertSessionHasNoErrors();
        $barang->refresh();
        $this->assertEquals(16, (float) $barang->stok);
        $this->assertEquals('draft', DB::table('retur_penjualan')->where('id', $returPenjualan->id)->value('status'));

        // Stok bersih seluruh alur tetap benar
        $this->assertEquals(16, (float) $barang->stok);
    }

    public function test_halaman_dan_api_empat_fitur_merender_tanpa_error(): void
    {
        $sup = $this->makeSupplier();
        $barang = $this->makeBarang();
        $cust = $this->makeCustomer();
        $pajak = Pajak::create(['nama' => 'PPN 11%', 'rate' => 11, 'is_aktif' => true]);
        $tanggal = now()->toDateString();

        $this->post(route('pembelian.store'), [
            'tanggal' => $tanggal, 'supplier_id' => $sup->id, 'metode_bayar' => 'kredit',
            'diskon' => 0, 'diskon_tipe' => 'nominal', 'sync_harga' => null, 'pajak_id' => $pajak->id,
            'items' => [['barang_id' => $barang->id, 'jumlah' => 10, 'harga_satuan' => 3000, 'diskon' => 0]],
        ])->assertSessionHasNoErrors();
        $this->post(route('penjualan.store'), [
            'tanggal' => $tanggal, 'customer_id' => $cust->id, 'metode_bayar' => 'kredit',
            'diskon' => 0, 'diskon_tipe' => 'nominal', 'sync_harga' => null, 'pajak_id' => $pajak->id,
            'items' => [['barang_id' => $barang->id, 'jumlah' => 4, 'harga_satuan' => 9000, 'diskon' => 0]],
        ])->assertSessionHasNoErrors();

        $ps = null;
        $so = null;
        $rpm = null;
        $rpj = null;

        // Perubahan Stok
        $this->post(route('perubahan-stok.store'), [
            'tanggal' => $tanggal, 'jenis' => 'rusak',
            'items' => [['barang_id' => $barang->id, 'arah' => 'keluar', 'jumlah' => 1]],
        ])->assertSessionHasNoErrors();
        $ps = DB::table('perubahan_stok')->orderByDesc('id')->value('id');

        // Stok Opname
        $this->post(route('stok-opname.store'), [
            'tanggal' => $tanggal,
            'items' => [['barang_id' => $barang->id, 'stok_fisik' => 10]],
        ])->assertSessionHasNoErrors();
        $so = DB::table('stok_opname')->orderByDesc('id')->value('id');

        // Retur Penjualan
        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $penjualanItem = DB::table('penjualan_items')->where('penjualan_id', $penjualan->id)->first();
        $this->post(route('retur-penjualan.store'), [
            'tanggal' => $tanggal, 'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $penjualanItem->id, 'jumlah' => 1]],
        ])->assertSessionHasNoErrors();
        $rpj = DB::table('retur_penjualan')->orderByDesc('id')->value('id');

        // Retur Pembelian
        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $pembelianItem = DB::table('pembelian_items')->where('pembelian_id', $pembelian->id)->first();
        $this->post(route('retur-pembelian.store'), [
            'tanggal' => $tanggal, 'pembelian_id' => $pembelian->id,
            'items' => [['pembelian_item_id' => $pembelianItem->id, 'jumlah' => 2]],
        ])->assertSessionHasNoErrors();
        $rpm = DB::table('retur_pembelian')->orderByDesc('id')->value('id');

        // Semua halaman index & create merender
        foreach (['perubahan-stok', 'stok-opname', 'retur-penjualan', 'retur-pembelian'] as $menu) {
            $this->get(route("{$menu}.index"))->assertOk();
            $this->get(route("{$menu}.create"))->assertOk();
        }

        // Semua halaman detail merender
        $this->get(route('perubahan-stok.show', $ps))->assertOk();
        $this->get(route('stok-opname.show', $so))->assertOk();
        $this->get(route('retur-penjualan.show', $rpj))->assertOk();
        $this->get(route('retur-pembelian.show', $rpm))->assertOk();

        // API sumber item retur mengembalikan harga neto
        $resp = $this->getJson(route('retur-penjualan.sumber-items', $penjualan->id))->assertOk()->json();
        $this->assertNotEmpty($resp);
        $this->assertArrayHasKey('harga', $resp[0]);
        $this->assertArrayHasKey('harga_satuan', $resp[0]);
        $this->assertEquals(9000, (float) $resp[0]['harga']);
        $this->assertEquals(3, (float) $resp[0]['sisa']);

        $resp = $this->getJson(route('retur-pembelian.sumber-items', $pembelian->id))->assertOk()->json();
        $this->assertNotEmpty($resp);
        $this->assertArrayHasKey('harga', $resp[0]);
        $this->assertEquals(3000, (float) $resp[0]['harga']);
        $this->assertEquals(8, (float) $resp[0]['sisa']);

        // API pencarian transaksi (combobox) hanya mengembalikan yang masih bisa diretur
        $cari = $this->getJson(route('retur-penjualan.search', ['q' => $cust->nama]))->assertOk()->json();
        $this->assertCount(1, $cari);
        $this->assertEquals($penjualan->id, $cari[0]['id']);
        $this->assertArrayHasKey('label', $cari[0]);

        $cari = $this->getJson(route('retur-pembelian.search', ['q' => $sup->nama]))->assertOk()->json();
        $this->assertCount(1, $cari);
        $this->assertEquals($pembelian->id, $cari[0]['id']);
        $this->assertArrayHasKey('label', $cari[0]);

        // Query yang tidak cocok mengembalikan daftar kosong
        $this->getJson(route('retur-penjualan.search', ['q' => 'TAK-ADA']))->assertOk()->assertExactJson([]);
        $this->getJson(route('retur-pembelian.search', ['q' => 'TAK-ADA']))->assertOk()->assertExactJson([]);
    }
}
