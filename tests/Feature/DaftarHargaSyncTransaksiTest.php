<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\DaftarHarga;
use App\Models\DaftarHargaRiwayat;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class DaftarHargaSyncTransaksiTest extends TestCase
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

    private function makeBarang(int $stok = 100): Barang
    {
        return Barang::create([
            'kode' => 'BRG-SYNC-'.substr((string) uniqid(), -5),
            'nama' => 'Barang Sync',
            'tipe' => 'barang',
            'stok' => $stok,
            'harga_beli' => 3000,
            'harga_jual' => 8000,
            'is_aktif' => true,
        ]);
    }

    private function makeSupplier(): Supplier
    {
        return Supplier::create(['kode' => 'SUP-SYNC-'.substr((string) uniqid(), -5), 'nama' => 'Supplier Sync', 'is_aktif' => true]);
    }

    private function makeCustomer(): Customer
    {
        return Customer::create(['kode' => 'CUST-SYNC-'.substr((string) uniqid(), -5), 'nama' => 'Customer Sync', 'is_aktif' => true]);
    }

    private function postPembelian(int $supplierId, int $barangId, float $qty, float $harga, ?string $sync = '1'): TestResponse
    {
        return $this->post(route('pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $supplierId,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => $sync,
            'items' => [
                ['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => $harga, 'diskon' => 0],
            ],
        ]);
    }

    private function postPenjualan(int $customerId, int $barangId, float $qty, float $harga, ?string $sync = '1'): TestResponse
    {
        return $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $customerId,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => $sync,
            'items' => [
                ['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => $harga, 'diskon' => 0],
            ],
        ]);
    }

    public function test_pembelian_sync_updates_daftar_harga_tier_dan_riwayat()
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        DaftarHarga::create(['entitas' => 'supplier', 'supplier_id' => $sup->id, 'barang_id' => $brg->id, 'harga' => 3000, 'min_qty' => 1, 'max_qty' => null, 'is_aktif' => true]);

        $this->postPembelian($sup->id, $brg->id, 5, 3200)->assertSessionHasNoErrors();

        $row = DaftarHarga::where('supplier_id', $sup->id)->where('barang_id', $brg->id)->first();
        $this->assertEquals(3200, (float) $row->harga);

        $this->assertDatabaseHas('daftar_harga_riwayat', [
            'entitas' => 'supplier',
            'supplier_id' => $sup->id,
            'barang_id' => $brg->id,
            'tipe' => DaftarHargaRiwayat::TIPE_UBAH,
            'harga_lama' => 3000,
            'harga_baru' => 3200,
            'min_qty_lama' => 1,
            'min_qty_baru' => 1,
        ]);
    }

    public function test_pembelian_sync_creates_base_tier_saat_belum_ada()
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();

        $this->assertDatabaseMissing('daftar_harga', ['entitas' => 'supplier', 'supplier_id' => $sup->id, 'barang_id' => $brg->id]);

        $this->postPembelian($sup->id, $brg->id, 3, 2600)->assertSessionHasNoErrors();

        $this->assertDatabaseHas('daftar_harga', [
            'entitas' => 'supplier',
            'supplier_id' => $sup->id,
            'barang_id' => $brg->id,
            'harga' => 2600,
            'min_qty' => 1,
            'max_qty' => null,
            'is_aktif' => 1,
        ]);
        $this->assertDatabaseHas('daftar_harga_riwayat', [
            'entitas' => 'supplier',
            'supplier_id' => $sup->id,
            'barang_id' => $brg->id,
            'tipe' => DaftarHargaRiwayat::TIPE_BUAT,
            'harga_baru' => 2600,
        ]);
    }

    public function test_pembelian_sync_off_tidak_mengubah_daftar_harga()
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        DaftarHarga::create(['entitas' => 'supplier', 'supplier_id' => $sup->id, 'barang_id' => $brg->id, 'harga' => 3000, 'min_qty' => 1, 'max_qty' => null, 'is_aktif' => true]);

        $this->postPembelian($sup->id, $brg->id, 5, 3200, null)->assertSessionHasNoErrors();

        $row = DaftarHarga::where('supplier_id', $sup->id)->where('barang_id', $brg->id)->first();
        $this->assertEquals(3000, (float) $row->harga);
        $this->assertEquals(0, DaftarHargaRiwayat::where('supplier_id', $sup->id)->where('barang_id', $brg->id)->count());
    }

    public function test_pembelian_sync_qty_diluar_range_membuat_tier_baru_tanpa_overlap()
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        DaftarHarga::create(['entitas' => 'supplier', 'supplier_id' => $sup->id, 'barang_id' => $brg->id, 'harga' => 5000, 'min_qty' => 1, 'max_qty' => 10, 'is_aktif' => true]);
        DaftarHarga::create(['entitas' => 'supplier', 'supplier_id' => $sup->id, 'barang_id' => $brg->id, 'harga' => 4500, 'min_qty' => 11, 'max_qty' => 100, 'is_aktif' => true]);

        // qty 150 tidak masuk tier manapun -> harga transaksi tetap dicatat sebagai tier baru (tanpa overlap)
        $this->postPembelian($sup->id, $brg->id, 150, 4600)->assertSessionHasNoErrors();

        $rows = DaftarHarga::where('supplier_id', $sup->id)->where('barang_id', $brg->id)->orderBy('min_qty')->get();
        $this->assertCount(3, $rows);
        $this->assertEquals(5000, (float) $rows[0]->harga);
        $this->assertEquals(4500, (float) $rows[1]->harga);
        $this->assertEquals(4600, (float) $rows[2]->harga);
        $this->assertEquals(150, (float) $rows[2]->min_qty);
        $this->assertNull($rows[2]->max_qty);
        $this->assertEquals(1, DaftarHargaRiwayat::where('supplier_id', $sup->id)->where('barang_id', $brg->id)->where('tipe', DaftarHargaRiwayat::TIPE_BUAT)->count());
    }

    public function test_sinkron_tidak_menulis_harga_nol_ke_daftar_harga()
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        DaftarHarga::create(['entitas' => 'supplier', 'supplier_id' => $sup->id, 'barang_id' => $brg->id, 'harga' => 3000, 'min_qty' => 1, 'max_qty' => null, 'is_aktif' => true]);

        // Harga satuan 0 (mis. user lupa mengisi) tidak boleh menimpa daftar harga
        $this->postPembelian($sup->id, $brg->id, 5, 0)->assertSessionHasNoErrors();

        $row = DaftarHarga::where('supplier_id', $sup->id)->where('barang_id', $brg->id)->first();
        $this->assertEquals(3000, (float) $row->harga);
        $this->assertEquals(0, DaftarHargaRiwayat::where('supplier_id', $sup->id)->where('barang_id', $brg->id)->count());
    }

    public function test_penjualan_sync_updates_daftar_harga_jual()
    {
        $this->login();
        $cust = $this->makeCustomer();
        $brg = $this->makeBarang();
        DaftarHarga::create(['entitas' => 'customer', 'customer_id' => $cust->id, 'barang_id' => $brg->id, 'harga' => 9000, 'min_qty' => 1, 'max_qty' => null, 'is_aktif' => true]);

        $this->postPenjualan($cust->id, $brg->id, 2, 9500)->assertSessionHasNoErrors();

        $row = DaftarHarga::where('entitas', 'customer')->where('customer_id', $cust->id)->where('barang_id', $brg->id)->first();
        $this->assertEquals(9500, (float) $row->harga);

        $this->assertDatabaseHas('daftar_harga_riwayat', [
            'entitas' => 'customer',
            'customer_id' => $cust->id,
            'barang_id' => $brg->id,
            'tipe' => DaftarHargaRiwayat::TIPE_UBAH,
            'harga_lama' => 9000,
            'harga_baru' => 9500,
        ]);
    }

    public function test_create_pages_menampilkan_sumber_harga_dan_harga_terakhir()
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        $cust = $this->makeCustomer();
        DaftarHarga::create(['entitas' => 'supplier', 'supplier_id' => $sup->id, 'barang_id' => $brg->id, 'harga' => 3000, 'min_qty' => 1, 'max_qty' => null, 'is_aktif' => true]);

        // Catat transaksi agar "Harga terakhir" muncul sebagai opsi
        $this->postPembelian($sup->id, $brg->id, 5, 3200, null)->assertSessionHasNoErrors();

        $this->get(route('pembelian.create'))
            ->assertStatus(200)
            ->assertSee('Sumber Harga')
            ->assertSee('Harga terakhir');

        $this->get(route('penjualan.create'))
            ->assertStatus(200)
            ->assertSee('Sumber Harga')
            ->assertSee('Jadikan harga ini acuan');
    }

    public function test_harga_akhir_sama_tidak_membuat_riwayat_baru()
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        DaftarHarga::create(['entitas' => 'supplier', 'supplier_id' => $sup->id, 'barang_id' => $brg->id, 'harga' => 3000, 'min_qty' => 1, 'max_qty' => null, 'is_aktif' => true]);

        $this->postPembelian($sup->id, $brg->id, 5, 3000)->assertSessionHasNoErrors();

        $this->assertEquals(0, DaftarHargaRiwayat::where('supplier_id', $sup->id)->where('barang_id', $brg->id)->count());
    }

    private function collectWarnings(): Collection
    {
        $warnings = collect();
        Log::listen(function ($event) use ($warnings) {
            if ($event->level === 'warning') {
                $warnings->push((string) $event->message);
            }
        });

        return $warnings;
    }

    public function test_pembelian_sync_error_dicatat_log_warning_dan_transaksi_tetap_tersimpan()
    {
        $this->login();
        $sup = $this->makeSupplier();
        $brg = $this->makeBarang();
        DaftarHarga::create(['entitas' => 'supplier', 'supplier_id' => $sup->id, 'barang_id' => $brg->id, 'harga' => 3000, 'min_qty' => 1, 'max_qty' => null, 'is_aktif' => true]);

        $warnings = $this->collectWarnings();

        // Harga melebihi kapasitas daftar_harga_riwayat (decimal 15,2) yang dijaga model
        // (sama dengan batas MySQL strict), sementara kolom daftar_harga (18,2) menampungnya
        // -> sinkronisasi melempar exception namun transaksi tetap tersimpan.
        $this->postPembelian($sup->id, $brg->id, 1, 90_000_000_000_000)->assertSessionHasNoErrors();

        $this->assertTrue($warnings->contains(fn ($m) => str_contains($m, 'Sinkron harga beli gagal')));
        $this->assertDatabaseHas('pembelians', ['supplier_id' => $sup->id]);
        $this->assertEquals(90_000_000_000_000, (float) DaftarHarga::where('supplier_id', $sup->id)->where('barang_id', $brg->id)->firstOrFail()->harga);
    }

    public function test_penjualan_sync_error_dicatat_log_warning_dan_transaksi_tetap_tersimpan()
    {
        $this->login();
        $cust = $this->makeCustomer();
        $brg = $this->makeBarang();
        DaftarHarga::create(['entitas' => 'customer', 'customer_id' => $cust->id, 'barang_id' => $brg->id, 'harga' => 9000, 'min_qty' => 1, 'max_qty' => null, 'is_aktif' => true]);

        $warnings = $this->collectWarnings();

        // Harga melebihi kapasitas daftar_harga_riwayat (decimal 15,2) yang dijaga model
        // -> sinkronisasi melempar exception namun transaksi tetap tersimpan.
        $this->postPenjualan($cust->id, $brg->id, 1, 90_000_000_000_000)->assertSessionHasNoErrors();

        $this->assertTrue($warnings->contains(fn ($m) => str_contains($m, 'Sinkron harga jual gagal')));
        $this->assertDatabaseHas('penjualans', ['customer_id' => $cust->id]);
        $this->assertEquals(90_000_000_000_000, (float) DaftarHarga::where('customer_id', $cust->id)->where('barang_id', $brg->id)->firstOrFail()->harga);
    }
}
