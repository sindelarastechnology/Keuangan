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

class InventoriTransaksiTest extends TestCase
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

    private function makeBarang(array $overrides = []): Barang
    {
        return Barang::create(array_merge([
            'kode' => 'BRG-'.substr((string) uniqid(), -5),
            'nama' => 'Barang Inventori',
            'tipe' => 'barang',
            'stok' => 0,
            'harga_beli' => 3000,
            'harga_jual' => 8000,
            'is_aktif' => true,
        ], $overrides));
    }

    private function makeSupplier(): Supplier
    {
        return Supplier::create(['kode' => 'SUP-'.substr((string) uniqid(), -5), 'nama' => 'Supplier Inventori', 'is_aktif' => true]);
    }

    private function beli(int $supplierId, int $barangId, float $qty, float $harga, string $metode = 'kredit'): void
    {
        $this->post(route('pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $supplierId,
            'metode_bayar' => $metode,
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [
                ['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => $harga, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();
    }

    private function jual(int $customerId, int $barangId, float $qty, float $hargaSatuan, string $metode = 'kredit'): void
    {
        $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $customerId,
            'metode_bayar' => $metode,
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [
                ['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => $hargaSatuan, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();
    }

    private function akunKode(string $kode): int
    {
        return (int) AkunPerkiraan::where('kode', $kode)->value('id');
    }

    public function test_perubahan_stok_arah_keluar_mengurangi_stok_dan_mencatat_penyesuaian(): void
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);

        $this->post(route('perubahan-stok.store'), [
            'tanggal' => now()->toDateString(),
            'jenis' => 'rusak',
            'keterangan' => 'Rusak saat uji coba',
            'items' => [
                ['barang_id' => $brg->id, 'arah' => 'keluar', 'jumlah' => 2],
            ],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(8, (float) $brg->stok);
        $this->assertEqualsWithDelta(3000, (float) $brg->harga_avg, 0.01);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('531'), 'debit' => 6000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 0, 'kredit' => 6000]);
        $this->assertDatabaseHas('perubahan_stok', ['jenis' => 'rusak', 'status' => 'posted']);
    }

    public function test_perubahan_stok_arah_masuk_menambah_stok_dan_mencatat_pendapatan_lain(): void
    {
        $this->login();
        $brg = $this->makeBarang(['stok' => 0, 'harga_avg' => 4000]);

        $this->post(route('perubahan-stok.store'), [
            'tanggal' => now()->toDateString(),
            'jenis' => 'lebih',
            'items' => [
                ['barang_id' => $brg->id, 'arah' => 'masuk', 'jumlah' => 2],
            ],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(2, (float) $brg->stok);
        $this->assertEqualsWithDelta(4000, (float) $brg->harga_avg, 0.01);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 8000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('421'), 'debit' => 0, 'kredit' => 8000]);
    }

    public function test_perubahan_stok_menolak_barang_tipe_jasa(): void
    {
        $this->login();
        $jasa = Barang::create([
            'kode' => 'JAS-'.substr((string) uniqid(), -5),
            'nama' => 'Jasa',
            'tipe' => 'jasa',
            'stok' => 0,
            'harga_beli' => 0,
            'harga_jual' => 100000,
            'is_aktif' => true,
        ]);

        $this->post(route('perubahan-stok.store'), [
            'tanggal' => now()->toDateString(),
            'jenis' => 'rusak',
            'items' => [
                ['barang_id' => $jasa->id, 'arah' => 'keluar', 'jumlah' => 1],
            ],
        ])->assertSessionHas('error');

        $this->assertDatabaseMissing('perubahan_stok', ['jenis' => 'rusak']);
        $this->assertDatabaseMissing('jurnal_umum', ['tipe' => 'penyesuaian_stok']);
    }

    public function test_perubahan_stok_void_mengembalikan_stok(): void
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);

        $this->post(route('perubahan-stok.store'), [
            'tanggal' => now()->toDateString(),
            'jenis' => 'rusak',
            'items' => [
                ['barang_id' => $brg->id, 'arah' => 'keluar', 'jumlah' => 2],
            ],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(8, (float) $brg->stok);

        $ps = DB::table('perubahan_stok')->orderByDesc('id')->first();
        $this->post(route('perubahan-stok.void', $ps->id))->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(10, (float) $brg->stok);
        $this->assertEquals('draft', DB::table('perubahan_stok')->where('id', $ps->id)->value('status'));
    }

    public function test_stok_opname_selisih_lebih_menambah_stok(): void
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);

        $this->post(route('stok-opname.store'), [
            'tanggal' => now()->toDateString(),
            'items' => [
                ['barang_id' => $brg->id, 'stok_fisik' => 12],
            ],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(12, (float) $brg->stok);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 6000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('421'), 'debit' => 0, 'kredit' => 6000]);
    }

    public function test_stok_opname_selisih_kurang_mengurangi_stok(): void
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);

        $this->post(route('stok-opname.store'), [
            'tanggal' => now()->toDateString(),
            'items' => [
                ['barang_id' => $brg->id, 'stok_fisik' => 8],
            ],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(8, (float) $brg->stok);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('531'), 'debit' => 6000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 0, 'kredit' => 6000]);
    }

    public function test_stok_opname_tanpa_selisih_tidak_membuat_jurnal(): void
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);

        $this->post(route('stok-opname.store'), [
            'tanggal' => now()->toDateString(),
            'items' => [
                ['barang_id' => $brg->id, 'stok_fisik' => 10],
            ],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(10, (float) $brg->stok);
        $this->assertDatabaseMissing('jurnal_umum', ['tipe' => 'penyesuaian_stok']);
    }

    public function test_stok_opname_void_mengembalikan_stok(): void
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);

        $this->post(route('stok-opname.store'), [
            'tanggal' => now()->toDateString(),
            'items' => [
                ['barang_id' => $brg->id, 'stok_fisik' => 12],
            ],
        ])->assertSessionHasNoErrors();
        $brg->refresh();
        $this->assertEquals(12, (float) $brg->stok);

        $so = DB::table('stok_opname')->orderByDesc('id')->first();
        $this->post(route('stok-opname.void', $so->id))->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(10, (float) $brg->stok);
        $this->assertEquals('draft', DB::table('stok_opname')->where('id', $so->id)->value('status'));
    }

    public function test_retur_penjualan_mengembalikan_stok_dan_mencatat_jurnal(): void
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);

        $cust = Customer::create(['kode' => 'CUST-'.substr((string) uniqid(), -5), 'nama' => 'Customer Retur', 'is_aktif' => true]);
        $this->jual($cust->id, $brg->id, 4, 9000);

        $brg->refresh();
        $this->assertEquals(6, (float) $brg->stok);

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $penjualanItem = DB::table('penjualan_items')->where('penjualan_id', $penjualan->id)->first();

        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'penjualan_id' => $penjualan->id,
            'items' => [
                ['penjualan_item_id' => $penjualanItem->id, 'jumlah' => 2],
            ],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(8, (float) $brg->stok);

        // Jurnal pengurangan pendapatan & piutang
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('411'), 'debit' => 18000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('113'), 'debit' => 0, 'kredit' => 18000]);

        // Jurnal reversal HPP
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 6000, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('51'), 'debit' => 0, 'kredit' => 6000]);

        // BB piutang tercatat
        $lastPiutang = BbPiutang::where('customer_id', $cust->id)->orderByDesc('id')->first();
        $this->assertNotNull($lastPiutang);
        $this->assertEqualsWithDelta(18000, (float) $lastPiutang->kredit, 0.01);
    }

    public function test_retur_penjualan_menolak_jumlah_melebihi_sisa(): void
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);

        $cust = Customer::create(['kode' => 'CUST-'.substr((string) uniqid(), -5), 'nama' => 'Customer Retur', 'is_aktif' => true]);
        $this->jual($cust->id, $brg->id, 4, 9000);

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $penjualanItem = DB::table('penjualan_items')->where('penjualan_id', $penjualan->id)->first();

        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'penjualan_id' => $penjualan->id,
            'items' => [
                ['penjualan_item_id' => $penjualanItem->id, 'jumlah' => 5],
            ],
        ])->assertSessionHas('error');

        $this->assertDatabaseMissing('retur_penjualan', ['status' => 'posted']);
    }

    public function test_retur_penjualan_void_mengembalikan_stok_dan_jurnal(): void
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);

        $cust = Customer::create(['kode' => 'CUST-'.substr((string) uniqid(), -5), 'nama' => 'Customer Retur', 'is_aktif' => true]);
        $this->jual($cust->id, $brg->id, 4, 9000);
        $brg->refresh();
        $this->assertEquals(6, (float) $brg->stok);

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $penjualanItem = DB::table('penjualan_items')->where('penjualan_id', $penjualan->id)->first();
        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'penjualan_id' => $penjualan->id,
            'items' => [
                ['penjualan_item_id' => $penjualanItem->id, 'jumlah' => 2],
            ],
        ])->assertSessionHasNoErrors();
        $brg->refresh();
        $this->assertEquals(8, (float) $brg->stok);

        $retur = DB::table('retur_penjualan')->orderByDesc('id')->first();
        $this->post(route('retur-penjualan.void', $retur->id))->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(6, (float) $brg->stok);
        $this->assertEquals('draft', DB::table('retur_penjualan')->where('id', $retur->id)->value('status'));
    }

    public function test_retur_pembelian_mengurangi_stok_dan_mencatat_jurnal(): void
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);
        $brg->refresh();
        $this->assertEquals(10, (float) $brg->stok);

        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $pembelianItem = DB::table('pembelian_items')->where('pembelian_id', $pembelian->id)->first();

        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'pembelian_id' => $pembelian->id,
            'items' => [
                ['pembelian_item_id' => $pembelianItem->id, 'jumlah' => 4],
            ],
        ])->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(6, (float) $brg->stok);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 0, 'kredit' => 12000]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('211'), 'debit' => 12000, 'kredit' => 0]);

        $lastHutang = BbHutang::where('supplier_id', $sup->id)->orderByDesc('id')->first();
        $this->assertNotNull($lastHutang);
        $this->assertEqualsWithDelta(12000, (float) $lastHutang->debit, 0.01);
    }

    public function test_retur_pembelian_menolak_jumlah_melebihi_sisa(): void
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);

        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $pembelianItem = DB::table('pembelian_items')->where('pembelian_id', $pembelian->id)->first();

        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'pembelian_id' => $pembelian->id,
            'items' => [
                ['pembelian_item_id' => $pembelianItem->id, 'jumlah' => 12],
            ],
        ])->assertSessionHas('error');

        $this->assertDatabaseMissing('retur_pembelian', ['status' => 'posted']);
    }

    public function test_retur_pembelian_void_mengembalikan_stok_dan_jurnal(): void
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);

        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $pembelianItem = DB::table('pembelian_items')->where('pembelian_id', $pembelian->id)->first();

        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'pembelian_id' => $pembelian->id,
            'items' => [
                ['pembelian_item_id' => $pembelianItem->id, 'jumlah' => 4],
            ],
        ])->assertSessionHasNoErrors();
        $brg->refresh();
        $this->assertEquals(6, (float) $brg->stok);

        $retur = DB::table('retur_pembelian')->orderByDesc('id')->first();
        $this->post(route('retur-pembelian.void', $retur->id))->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(10, (float) $brg->stok);
        $this->assertEquals('draft', DB::table('retur_pembelian')->where('id', $retur->id)->value('status'));
    }

    public function test_retur_penjualan_menggunakan_nilai_neto_dan_membalik_ppn_saat_ada_diskon(): void
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $this->beli($sup->id, $brg->id, 10, 3000);

        $cust = Customer::create(['kode' => 'CUST-'.substr((string) uniqid(), -5), 'nama' => 'Customer Diskon', 'is_aktif' => true]);
        $pajak = Pajak::create(['nama' => 'PPN 11%', 'rate' => 11, 'is_aktif' => true]);

        $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $cust->id,
            'metode_bayar' => 'kredit',
            'diskon' => 5000,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'pajak_id' => $pajak->id,
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 4, 'harga_satuan' => 9000, 'diskon' => 2000],
            ],
        ])->assertSessionHasNoErrors();

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $this->assertEquals(34000, (float) $penjualan->subtotal);
        $this->assertEquals(5000, (float) $penjualan->diskon_nominal);
        $this->assertEquals(3190, (float) $penjualan->pajak_nominal);

        $penjualanItem = DB::table('penjualan_items')->where('penjualan_id', $penjualan->id)->first();

        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'penjualan_id' => $penjualan->id,
            'items' => [
                ['penjualan_item_id' => $penjualanItem->id, 'jumlah' => 2],
            ],
        ])->assertSessionHasNoErrors();

        // Nilai retur = proporsi nilai neto (setelah diskon item & global) yang diakui di akun 411
        $returItem = DB::table('retur_penjualan_items')->orderByDesc('id')->first();
        $this->assertEquals(7250, (float) $returItem->harga_satuan);
        $this->assertEquals(14500, (float) $returItem->subtotal);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('411'), 'debit' => 14500, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('212'), 'debit' => 1595, 'kredit' => 0]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('113'), 'debit' => 0, 'kredit' => 16095]);

        $lastPiutang = BbPiutang::where('customer_id', $cust->id)->orderByDesc('id')->first();
        $this->assertEqualsWithDelta(16095, (float) $lastPiutang->kredit, 0.01);
    }

    public function test_retur_pembelian_membalik_ppn_masukan_saat_ada_diskon(): void
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $pajak = Pajak::create(['nama' => 'PPN 11%', 'rate' => 11, 'is_aktif' => true]);

        $this->post(route('pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $sup->id,
            'metode_bayar' => 'kredit',
            'diskon' => 1000,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'pajak_id' => $pajak->id,
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 10, 'harga_satuan' => 3000, 'diskon' => 2000],
            ],
        ])->assertSessionHasNoErrors();

        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $this->assertEquals(28000, (float) $pembelian->subtotal);
        $this->assertEquals(27000, (float) $pembelian->subtotal - (float) $pembelian->diskon_nominal);
        $this->assertEquals(2970, (float) $pembelian->pajak_nominal);

        $pembelianItem = DB::table('pembelian_items')->where('pembelian_id', $pembelian->id)->first();
        $this->assertEquals(2700, (float) $pembelianItem->harga_net);

        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'pembelian_id' => $pembelian->id,
            'items' => [
                ['pembelian_item_id' => $pembelianItem->id, 'jumlah' => 4],
            ],
        ])->assertSessionHasNoErrors();

        $returItem = DB::table('retur_pembelian_items')->orderByDesc('id')->first();
        $this->assertEquals(10800, (float) $returItem->subtotal);

        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('115'), 'debit' => 0, 'kredit' => 10800]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('213'), 'debit' => 0, 'kredit' => 1188]);
        $this->assertDatabaseHas('jurnal_items', ['akun_id' => $this->akunKode('211'), 'debit' => 11988, 'kredit' => 0]);

        $lastHutang = BbHutang::where('supplier_id', $sup->id)->orderByDesc('id')->first();
        $this->assertEqualsWithDelta(11988, (float) $lastHutang->debit, 0.01);
    }

    public function test_stok_opname_dengan_harga_avg_nol_tetap_tersimpan_tanpa_jurnal(): void
    {
        $this->login();
        $brg = $this->makeBarang(['stok' => 5, 'harga_avg' => 0]);

        $response = $this->post(route('stok-opname.store'), [
            'tanggal' => now()->toDateString(),
            'items' => [
                ['barang_id' => $brg->id, 'stok_fisik' => 7],
            ],
        ]);

        $response->assertSessionHas('success');

        $brg->refresh();
        $this->assertEquals(7, (float) $brg->stok);
        $this->assertDatabaseMissing('jurnal_umum', ['tipe' => 'penyesuaian_stok']);
    }

    public function test_perubahan_stok_dengan_harga_avg_nol_tetap_tersimpan_tanpa_jurnal(): void
    {
        $this->login();
        $brg = $this->makeBarang(['stok' => 0, 'harga_avg' => 0]);

        $response = $this->post(route('perubahan-stok.store'), [
            'tanggal' => now()->toDateString(),
            'jenis' => 'lebih',
            'items' => [
                ['barang_id' => $brg->id, 'arah' => 'masuk', 'jumlah' => 2],
            ],
        ]);

        $response->assertSessionHas('success');

        $brg->refresh();
        $this->assertEquals(2, (float) $brg->stok);
        $this->assertDatabaseMissing('jurnal_umum', ['tipe' => 'penyesuaian_stok']);
    }
}
