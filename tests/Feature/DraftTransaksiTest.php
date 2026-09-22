<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\BbHutang;
use App\Models\BbPiutang;
use App\Models\Customer;
use App\Models\JurnalUmum;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class DraftTransaksiTest extends TestCase
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

    private function makeBarang(int $stok = 100, bool $jual = true): array
    {
        $brg = Barang::create([
            'kode' => 'BRG-'.substr((string) uniqid(), -5),
            'nama' => 'Barang Draft',
            'tipe' => 'barang',
            'stok' => $stok,
            'harga_beli' => 3000,
            'harga_jual' => 8000,
            'harga_avg' => 3000,
            'is_aktif' => true,
        ]);
        $rekanan = $jual
            ? Customer::create(['kode' => 'CUST-'.substr((string) uniqid(), -5), 'nama' => 'Customer Draft', 'is_aktif' => true])
            : Supplier::create(['kode' => 'SUP-'.substr((string) uniqid(), -5), 'nama' => 'Supplier Draft', 'is_aktif' => true]);

        return [$brg, $rekanan];
    }

    private function postPenjualan(int $customerId, int $barangId, float $qty, float $harga, string $aksi = 'posted', string $metode = 'kredit'): TestResponse
    {
        return $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $customerId,
            'aksi' => $aksi,
            'metode_bayar' => $metode,
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [
                ['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => $harga, 'diskon' => 0],
            ],
        ]);
    }

    private function postPembelian(int $supplierId, int $barangId, float $qty, float $harga, string $aksi = 'posted', string $metode = 'kredit'): TestResponse
    {
        return $this->post(route('pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $supplierId,
            'aksi' => $aksi,
            'metode_bayar' => $metode,
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [
                ['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => $harga, 'diskon' => 0],
            ],
        ]);
    }

    public function test_penjualan_draft_tersimpan_tanpa_stok_jurnal_atau_piutang(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);

        $this->postPenjualan($cust->id, $brg->id, 4, 9000, 'pending')->assertSessionHasNoErrors();

        $penjualan = Penjualan::orderByDesc('id')->firstOrFail();
        $this->assertEquals('pending', $penjualan->status);
        $this->assertEquals(36000, (float) $penjualan->total);

        // Draft tidak menyentuh stok, jurnal, maupun BB piutang
        $brg->refresh();
        $this->assertEquals(10, (float) $brg->stok);
        $this->assertEquals(0, JurnalUmum::where('tipe', 'penjualan')->where('ref_id', $penjualan->id)->count());
        $this->assertEquals(0, BbPiutang::where('customer_id', $cust->id)->count());
        $this->assertDatabaseHas('penjualan_items', ['penjualan_id' => $penjualan->id, 'barang_id' => $brg->id, 'jumlah' => 4]);
    }

    public function test_penjualan_draft_dapat_diposting(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);

        $this->postPenjualan($cust->id, $brg->id, 4, 9000, 'pending')->assertSessionHasNoErrors();
        $penjualan = Penjualan::orderByDesc('id')->firstOrFail();

        $this->post(route('penjualan.post', $penjualan))->assertSessionHasNoErrors();

        $penjualan->refresh();
        $this->assertEquals('posted', $penjualan->status);
        $brg->refresh();
        $this->assertEquals(6, (float) $brg->stok);
        $this->assertEquals(1, JurnalUmum::where('tipe', 'penjualan')->where('ref_id', $penjualan->id)->count());
        $this->assertEquals(1, BbPiutang::where('customer_id', $cust->id)->count());
    }

    public function test_penjualan_draft_post_tolak_saat_stok_tidak_cukup(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(3);

        // Draft boleh disimpan melebihi stok tersedia
        $this->postPenjualan($cust->id, $brg->id, 5, 9000, 'pending')->assertSessionHasNoErrors();
        $penjualan = Penjualan::orderByDesc('id')->firstOrFail();

        $this->post(route('penjualan.post', $penjualan))
            ->assertSessionHasErrors('items');

        $penjualan->refresh();
        $this->assertEquals('pending', $penjualan->status);
        $this->assertEquals(3, (float) $brg->fresh()->stok);
    }

    public function test_penjualan_posted_langsung_ditolak_saat_stok_tidak_cukup(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(3);

        $this->postPenjualan($cust->id, $brg->id, 5, 9000, 'posted')
            ->assertSessionHasErrors('items');

        $this->assertEquals(0, Penjualan::count());
        $this->assertEquals(3, (float) $brg->fresh()->stok);
        $this->assertEquals(0, JurnalUmum::count());
    }

    public function test_penjualan_draft_dibatalkan_menjadi_status_draft(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);

        $this->postPenjualan($cust->id, $brg->id, 4, 9000, 'pending')->assertSessionHasNoErrors();
        $penjualan = Penjualan::orderByDesc('id')->firstOrFail();

        $this->post(route('penjualan.void', $penjualan))->assertSessionHasNoErrors();

        $this->assertEquals('draft', $penjualan->fresh()->status);
        $this->assertEquals(10, (float) $brg->fresh()->stok);
    }

    public function test_pembelian_draft_tersimpan_tanpa_stok_jurnal_atau_hutang(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(0, false);

        $this->postPembelian($sup->id, $brg->id, 10, 3000, 'pending')->assertSessionHasNoErrors();

        $pembelian = Pembelian::orderByDesc('id')->firstOrFail();
        $this->assertEquals('pending', $pembelian->status);
        $this->assertEquals(0, (float) $brg->fresh()->stok);
        $this->assertEquals(0, JurnalUmum::where('tipe', 'pembelian')->where('ref_id', $pembelian->id)->count());
        $this->assertEquals(0, BbHutang::where('supplier_id', $sup->id)->count());
        $this->assertDatabaseHas('pembelian_items', ['pembelian_id' => $pembelian->id, 'barang_id' => $brg->id, 'jumlah' => 10]);
    }

    public function test_pembelian_draft_dapat_diposting(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(0, false);

        $this->postPembelian($sup->id, $brg->id, 10, 3000, 'pending')->assertSessionHasNoErrors();
        $pembelian = Pembelian::orderByDesc('id')->firstOrFail();

        $this->post(route('pembelian.post', $pembelian))->assertSessionHasNoErrors();

        $pembelian->refresh();
        $this->assertEquals('posted', $pembelian->status);
        $brg->refresh();
        $this->assertEquals(10, (float) $brg->stok);
        $this->assertEqualsWithDelta(3000, (float) $brg->harga_avg, 0.01);
        $this->assertEquals(1, JurnalUmum::where('tipe', 'pembelian')->where('ref_id', $pembelian->id)->count());
        $this->assertEquals(1, BbHutang::where('supplier_id', $sup->id)->count());
    }

    public function test_post_menolak_transaksi_yang_bukan_draft(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(0, false);

        $this->postPembelian($sup->id, $brg->id, 5, 3000)->assertSessionHasNoErrors();
        $pembelian = Pembelian::orderByDesc('id')->firstOrFail();

        $this->post(route('pembelian.post', $pembelian))
            ->assertSessionHas('error');

        $this->assertEquals('posted', $pembelian->fresh()->status);
    }

    public function test_aksi_tidak_valid_ditolak(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);

        $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $cust->id,
            'aksi' => 'final',
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 1, 'harga_satuan' => 9000, 'diskon' => 0],
            ],
        ])->assertSessionHasErrors('aksi');
    }

    public function test_penjualan_edit_halaman_hanya_untuk_draft(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);

        $this->postPenjualan($cust->id, $brg->id, 4, 9000, 'pending')->assertSessionHasNoErrors();
        $pend = Penjualan::orderByDesc('id')->firstOrFail();
        $this->get(route('penjualan.edit', $pend))->assertOk();

        $this->postPenjualan($cust->id, $brg->id, 1, 9000)->assertSessionHasNoErrors();
        $posted = Penjualan::orderByDesc('id')->firstOrFail();
        $this->get(route('penjualan.edit', $posted))->assertRedirect();
    }

    public function test_penjualan_draft_dapat_diedit(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);

        $this->postPenjualan($cust->id, $brg->id, 4, 9000, 'pending')->assertSessionHasNoErrors();
        $penjualan = Penjualan::orderByDesc('id')->firstOrFail();

        $this->put(route('penjualan.update', $penjualan), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $cust->id,
            'aksi' => 'pending',
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 6, 'harga_satuan' => 8000, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        $penjualan->refresh();
        $this->assertEquals('pending', $penjualan->status);
        $this->assertEquals(48000, (float) $penjualan->total);
        $this->assertEquals(10, (float) $brg->fresh()->stok);
        $this->assertEquals(0, JurnalUmum::where('tipe', 'penjualan')->where('ref_id', $penjualan->id)->count());
        $this->assertEquals(0, BbPiutang::where('customer_id', $cust->id)->count());
        $this->assertDatabaseHas('penjualan_items', ['penjualan_id' => $penjualan->id, 'barang_id' => $brg->id, 'jumlah' => 6]);
    }

    public function test_penjualan_draft_edit_langsung_diposting(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);

        $this->postPenjualan($cust->id, $brg->id, 4, 9000, 'pending')->assertSessionHasNoErrors();
        $penjualan = Penjualan::orderByDesc('id')->firstOrFail();

        $this->put(route('penjualan.update', $penjualan), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $cust->id,
            'aksi' => 'posted',
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 6, 'harga_satuan' => 8000, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        $penjualan->refresh();
        $this->assertEquals('posted', $penjualan->status);
        $this->assertEquals(4, (float) $brg->fresh()->stok);
        $this->assertEquals(1, JurnalUmum::where('tipe', 'penjualan')->where('ref_id', $penjualan->id)->count());
        $this->assertEquals(1, BbPiutang::where('customer_id', $cust->id)->count());
    }

    public function test_penjualan_bukan_draft_tidak_bisa_diedit(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);

        $this->postPenjualan($cust->id, $brg->id, 2, 9000)->assertSessionHasNoErrors();
        $posted = Penjualan::orderByDesc('id')->firstOrFail();

        $this->put(route('penjualan.update', $posted), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $cust->id,
            'aksi' => 'pending',
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 1, 'harga_satuan' => 9000, 'diskon' => 0],
            ],
        ])->assertSessionHas('error');

        $this->assertEquals('posted', $posted->fresh()->status);

        $this->postPenjualan($cust->id, $brg->id, 3, 9000, 'pending')->assertSessionHasNoErrors();
        $pend = Penjualan::where('status', 'pending')->firstOrFail();
        $this->post(route('penjualan.void', $pend))->assertSessionHasNoErrors();

        $this->put(route('penjualan.update', $pend), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $cust->id,
            'aksi' => 'pending',
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 1, 'harga_satuan' => 9000, 'diskon' => 0],
            ],
        ])->assertSessionHas('error');

        $this->assertEquals('draft', $pend->fresh()->status);
    }

    public function test_pembelian_edit_halaman_hanya_untuk_draft(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(0, false);

        $this->postPembelian($sup->id, $brg->id, 10, 3000, 'pending')->assertSessionHasNoErrors();
        $pend = Pembelian::orderByDesc('id')->firstOrFail();
        $this->get(route('pembelian.edit', $pend))->assertOk();

        $this->postPembelian($sup->id, $brg->id, 1, 3000)->assertSessionHasNoErrors();
        $posted = Pembelian::orderByDesc('id')->firstOrFail();
        $this->get(route('pembelian.edit', $posted))->assertRedirect();
    }

    public function test_pembelian_draft_dapat_diedit(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(0, false);

        $this->postPembelian($sup->id, $brg->id, 10, 3000, 'pending')->assertSessionHasNoErrors();
        $pembelian = Pembelian::orderByDesc('id')->firstOrFail();

        $this->put(route('pembelian.update', $pembelian), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $sup->id,
            'aksi' => 'pending',
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 12, 'harga_satuan' => 2500, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        $pembelian->refresh();
        $this->assertEquals('pending', $pembelian->status);
        $this->assertEquals(30000, (float) $pembelian->total);
        $this->assertEquals(0, (float) $brg->fresh()->stok);
        $this->assertEquals(0, JurnalUmum::where('tipe', 'pembelian')->where('ref_id', $pembelian->id)->count());
        $this->assertEquals(0, BbHutang::where('supplier_id', $sup->id)->count());
        $this->assertDatabaseHas('pembelian_items', ['pembelian_id' => $pembelian->id, 'barang_id' => $brg->id, 'jumlah' => 12]);
    }

    public function test_pembelian_draft_edit_langsung_diposting(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(0, false);

        $this->postPembelian($sup->id, $brg->id, 10, 3000, 'pending')->assertSessionHasNoErrors();
        $pembelian = Pembelian::orderByDesc('id')->firstOrFail();

        $this->put(route('pembelian.update', $pembelian), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $sup->id,
            'aksi' => 'posted',
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 12, 'harga_satuan' => 2500, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        $pembelian->refresh();
        $this->assertEquals('posted', $pembelian->status);
        $brg->refresh();
        $this->assertEquals(12, (float) $brg->stok);
        $this->assertEqualsWithDelta(2500, (float) $brg->harga_avg, 0.01);
        $this->assertEquals(1, JurnalUmum::where('tipe', 'pembelian')->where('ref_id', $pembelian->id)->count());
        $this->assertEquals(1, BbHutang::where('supplier_id', $sup->id)->count());
    }

    public function test_pembelian_bukan_draft_tidak_bisa_diedit(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(0, false);

        $this->postPembelian($sup->id, $brg->id, 2, 3000)->assertSessionHasNoErrors();
        $posted = Pembelian::orderByDesc('id')->firstOrFail();

        $this->put(route('pembelian.update', $posted), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $sup->id,
            'aksi' => 'pending',
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 1, 'harga_satuan' => 3000, 'diskon' => 0],
            ],
        ])->assertSessionHas('error');

        $this->assertEquals('posted', $posted->fresh()->status);

        $this->postPembelian($sup->id, $brg->id, 3, 3000, 'pending')->assertSessionHasNoErrors();
        $pend = Pembelian::where('status', 'pending')->firstOrFail();
        $this->post(route('pembelian.void', $pend))->assertSessionHasNoErrors();

        $this->put(route('pembelian.update', $pend), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $sup->id,
            'aksi' => 'pending',
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 1, 'harga_satuan' => 3000, 'diskon' => 0],
            ],
        ])->assertSessionHas('error');

        $this->assertEquals('draft', $pend->fresh()->status);
    }

    public function test_dashboard_total_penjualan_pembelian_hanya_status_posted(): void
    {
        $this->login();
        [$brgJ, $cust] = $this->makeBarang(30);
        [$brgB, $sup] = $this->makeBarang(30, false);

        $this->postPenjualan($cust->id, $brgJ->id, 4, 9000, 'pending')->assertSessionHasNoErrors();
        $this->postPembelian($sup->id, $brgB->id, 10, 3000, 'pending')->assertSessionHasNoErrors();
        $this->postPenjualan($cust->id, $brgJ->id, 2, 9000)->assertSessionHasNoErrors();
        $this->postPembelian($sup->id, $brgB->id, 5, 3000)->assertSessionHasNoErrors();

        $response = $this->get(route('dashboard'))->assertOk();
        $response->assertViewHas('totalPenjualan', 18000.0);
        $response->assertViewHas('totalPembelian', 15000.0);
        $response->assertViewHas('draftPenjualan', ['count' => 1, 'total' => 36000.0]);
        $response->assertViewHas('draftPembelian', ['count' => 1, 'total' => 30000.0]);
        $response->assertSee('draft penjualan');
        $response->assertSee('draft pembelian');
    }

    public function test_draft_dibatalkan_tidak_dihitung_dan_index_menandai_batal(): void
    {
        $this->login();
        [$brgJ, $cust] = $this->makeBarang(20);
        [$brgB, $sup] = $this->makeBarang(20, false);

        $this->postPenjualan($cust->id, $brgJ->id, 4, 9000, 'pending')->assertSessionHasNoErrors();
        $penjualan = Penjualan::where('status', 'pending')->firstOrFail();
        $this->post(route('penjualan.void', $penjualan))->assertSessionHasNoErrors();

        $this->postPembelian($sup->id, $brgB->id, 10, 3000, 'pending')->assertSessionHasNoErrors();
        $pembelian = Pembelian::where('status', 'pending')->firstOrFail();
        $this->post(route('pembelian.void', $pembelian))->assertSessionHasNoErrors();

        $this->get(route('penjualan.index'))->assertOk()->assertSee('Batal');
        $this->get(route('pembelian.index'))->assertOk()->assertSee('Batal');

        $response = $this->get(route('dashboard'))->assertOk();
        $response->assertViewHas('totalPenjualan', 0.0);
        $response->assertViewHas('totalPembelian', 0.0);
        $response->assertViewHas('draftPenjualan', ['count' => 0, 'total' => 0.0]);
        $response->assertViewHas('draftPembelian', ['count' => 0, 'total' => 0.0]);
    }
}
