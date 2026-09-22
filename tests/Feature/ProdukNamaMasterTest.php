<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\BbPersediaan;
use App\Models\BbPiutang;
use App\Models\Customer;
use App\Models\DaftarHarga;
use App\Models\Gudang;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\StokGudang;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProdukNamaMasterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function login(): void
    {
        $this->actingAs(User::where('email', 'admin@keuangan.test')->firstOrFail());
    }

    private function buatBarang(array $overrides = []): Barang
    {
        return Barang::create(array_merge([
            'kode' => 'BRG-7001',
            'nama' => 'Produk Reguler',
            'tipe' => 'barang',
            'satuan' => 'pcs',
            'stok' => 5,
            'harga_beli' => 6000,
            'harga_jual' => 12000,
            'is_aktif' => true,
        ], $overrides));
    }

    private function buatJasa(array $overrides = []): Barang
    {
        return Barang::create(array_merge([
            'kode' => 'JAS-7001',
            'nama' => 'Jasa Pemasangan',
            'tipe' => 'jasa',
            'satuan' => 'paket',
            'harga_jual' => 150000,
            'is_aktif' => true,
        ], $overrides));
    }

    public function test_index_data_produk_menampilkan_kedua_tipe_dan_filter(): void
    {
        $this->login();
        $this->buatBarang();
        $this->buatJasa();

        $this->get(route('data-produk.index'))
            ->assertOk()
            ->assertSee('Produk Reguler')
            ->assertSee('Jasa Pemasangan')
            ->assertSee('Atur Kolom Tabel');

        $this->get(route('data-produk.index', ['tipe' => 'barang']))
            ->assertOk()
            ->assertSee('Produk Reguler')
            ->assertDontSee('Jasa Pemasangan');

        $this->get(route('data-produk.index', ['tipe' => 'jasa']))
            ->assertOk()
            ->assertSee('Jasa Pemasangan')
            ->assertDontSee('Produk Reguler');
    }

    public function test_halaman_form_data_produk_merender(): void
    {
        $this->login();
        $this->buatBarang();

        $this->get(route('data-produk.create'))->assertOk()->assertSee('Data Produk');
        $this->get(route('data-produk.edit', $this->buatJasa()))->assertOk()->assertSee('Jasa');
    }

    public function test_store_barang_membuat_kode_dan_saldo_awal(): void
    {
        $this->login();

        $this->post(route('data-produk.store'), [
            'tipe' => 'barang',
            'nama' => 'Kaos Baru',
            'satuan' => 'pcs',
            'kategori' => 'Kain',
            'stok' => 8,
            'harga_beli' => 15000,
            'harga_jual' => 25000,
            'min_stok' => 2,
            'is_aktif' => '1',
        ])->assertSessionHasNoErrors()->assertRedirect(route('data-produk.index', ['tipe' => 'barang']));

        $barang = Barang::where('nama', 'Kaos Baru')->firstOrFail();
        $this->assertStringStartsWith('BRG-', $barang->kode);
        $this->assertSame('barang', $barang->tipe);
        $this->assertEquals(15000, (float) $barang->harga_avg);
        $this->assertEquals((int) Gudang::utama()->id, (int) $barang->gudang_id);

        $this->assertEquals(8, (float) StokGudang::where('barang_id', $barang->id)->where('gudang_id', $barang->gudang_id)->value('qty'));
        $this->assertTrue(BbPersediaan::where('barang_id', $barang->id)->exists());
        $this->assertEquals(8, (float) BbPersediaan::where('barang_id', $barang->id)->value('masuk_qty'));
        $this->assertEquals((float) (8 * 15000), (float) BbPersediaan::where('barang_id', $barang->id)->value('saldo_harga'));
    }

    public function test_store_jasa_membuat_kode_dan_stok_nol(): void
    {
        $this->login();

        $this->post(route('data-produk.store'), [
            'tipe' => 'jasa',
            'nama' => 'Jasa Perawatan',
            'harga_jual' => 75000,
            'is_aktif' => '1',
        ])->assertSessionHasNoErrors()->assertRedirect(route('data-produk.index', ['tipe' => 'jasa']));

        $jasa = Barang::where('nama', 'Jasa Perawatan')->firstOrFail();
        $this->assertStringStartsWith('JAS-', $jasa->kode);
        $this->assertSame('jasa', $jasa->tipe);
        $this->assertEquals(0, (float) $jasa->stok);
        $this->assertEquals(0, (float) $jasa->harga_beli);
        $this->assertEquals(0, (float) $jasa->harga_avg);
        $this->assertEquals(75000, (float) $jasa->harga_jual);
    }

    public function test_update_tipe_produk_terkunci(): void
    {
        $this->login();
        $jasa = $this->buatJasa();

        $this->put(route('data-produk.update', $jasa), [
            'tipe' => 'barang',
            'nama' => 'Jasa yang Diperbarui',
            'stok' => 50,
            'harga_beli' => 200,
            'harga_jual' => 90000,
            'min_stok' => 1,
            'is_aktif' => '1',
        ])->assertSessionHasNoErrors()->assertRedirect(route('data-produk.index', ['tipe' => 'jasa']));

        $fresh = $jasa->fresh();
        $this->assertSame('jasa', $fresh->tipe);
        $this->assertEquals(0, (float) $fresh->stok);
        $this->assertEquals(90000, (float) $fresh->harga_jual);
    }

    public function test_destroy_produk_dengan_daftar_harga_tapi_belum_dipakai_dihapus_permanen(): void
    {
        $this->login();
        $barang = $this->buatBarang();
        $supplier = Supplier::create(['kode' => 'SUP-7001', 'nama' => 'Pemasok Tes', 'is_aktif' => true]);
        DaftarHarga::create([
            'entitas' => 'supplier',
            'supplier_id' => $supplier->id,
            'barang_id' => $barang->id,
            'harga' => 100,
            'is_aktif' => true,
            'tanggal_mulai' => now()->toDateString(),
        ]);

        $this->delete(route('data-produk.destroy', $barang))
            ->assertRedirect(route('data-produk.index', ['tipe' => 'barang']))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('barang', ['id' => $barang->id]);
        $this->assertDatabaseMissing('daftar_harga', ['barang_id' => $barang->id]);
    }

    public function test_destroy_produk_tanpa_referensi_berhasil(): void
    {
        $this->login();
        $jasa = $this->buatJasa();

        $this->delete(route('data-produk.destroy', $jasa))
            ->assertRedirect(route('data-produk.index', ['tipe' => 'jasa']))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('barang', ['id' => $jasa->id]);
    }

    public function test_index_data_nama_menampilkan_kedua_entitas(): void
    {
        $this->login();
        Customer::create(['kode' => 'CUS-7001', 'nama' => 'Toko Maju', 'is_aktif' => true]);
        Supplier::create(['kode' => 'SUP-7001', 'nama' => 'PT Pemasok Jaya', 'is_aktif' => true]);

        $this->get(route('data-nama.index'))
            ->assertOk()
            ->assertSee('Toko Maju')
            ->assertDontSee('PT Pemasok Jaya');

        $this->get(route('data-nama.index', ['entitas' => 'supplier']))
            ->assertOk()
            ->assertSee('PT Pemasok Jaya')
            ->assertDontSee('Toko Maju');
    }

    public function test_store_data_nama_supplier_membuat_kode(): void
    {
        $this->login();

        $this->post(route('data-nama.store'), [
            'entitas' => 'supplier',
            'nama' => 'Distributor Baru',
            'telepon' => '021555',
            'is_aktif' => '1',
        ])->assertSessionHasNoErrors()->assertRedirect(route('data-nama.index', ['entitas' => 'supplier']));

        $supplier = Supplier::where('nama', 'Distributor Baru')->firstOrFail();
        $this->assertStringStartsWith('SUP-', $supplier->kode);
    }

    public function test_update_data_nama_customer(): void
    {
        $this->login();
        $customer = Customer::create(['kode' => 'CUS-7002', 'nama' => 'Toko Lama', 'is_aktif' => true]);

        $this->put(route('data-nama.update', ['entitas' => 'customer', 'dataNama' => $customer->id]), [
            'entitas' => 'customer',
            'nama' => 'Toko Sejahtera',
            'is_aktif' => '1',
        ])->assertSessionHasNoErrors()->assertRedirect(route('data-nama.index', ['entitas' => 'customer']));

        $this->assertEquals('Toko Sejahtera', $customer->fresh()->nama);
    }

    public function test_destroy_data_nama_dengan_piutang_ditolak(): void
    {
        $this->login();
        $customer = Customer::create(['kode' => 'CUS-7003', 'nama' => 'Toko Berutang', 'is_aktif' => true]);
        BbPiutang::create([
            'customer_id' => $customer->id,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Piutang tes',
            'debit' => 0,
            'kredit' => 100000,
            'saldo' => -100000,
        ]);

        $this->delete(route('data-nama.destroy', ['entitas' => 'customer', 'dataNama' => $customer->id]))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }

    public function test_destroy_data_nama_tanpa_referensi_berhasil(): void
    {
        $this->login();
        $supplier = Supplier::create(['kode' => 'SUP-7002', 'nama' => 'Pemasok Lepas', 'is_aktif' => true]);

        $this->delete(route('data-nama.destroy', ['entitas' => 'supplier', 'dataNama' => $supplier->id]))
            ->assertRedirect(route('data-nama.index', ['entitas' => 'supplier']))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_akses_satuan_dan_kategori_tetap_tersedia_di_form_produk(): void
    {
        $this->login();
        Satuan::factory()->create(['nama' => 'lusin']);
        Kategori::factory()->create(['nama' => 'Rokok']);

        $this->get(route('data-produk.create'))
            ->assertOk()
            ->assertSee('lusin')
            ->assertSee('Rokok');
    }
}
