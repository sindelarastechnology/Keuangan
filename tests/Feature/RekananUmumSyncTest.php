<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\DaftarHarga;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekananUmumSyncTest extends TestCase
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
            'kode' => 'BRG-UMUM-'.substr((string) uniqid(), -5),
            'nama' => 'Barang UMUM',
            'tipe' => 'barang',
            'stok' => 50,
            'harga_avg' => 3000,
            'min_stok' => 1,
            'harga_beli' => 3000,
            'harga_jual' => 8000,
            'is_aktif' => true,
        ], $overrides));
    }

    public function test_migrasi_menyediakan_supplier_dan_customer_umum(): void
    {
        $this->assertNotNull(Supplier::umum());
        $this->assertSame('UMUM', Supplier::umum()->nama);
        $this->assertSame('UMUM', Customer::umum()->nama);
    }

    public function test_store_barang_menyinkronkan_harga_beli_dan_jual_ke_rekanan_umum(): void
    {
        $this->login();

        $this->post(route('barang.store'), [
            'nama' => 'Beras Premium',
            'satuan' => 'karung',
            'harga_avg' => 12000,
            'harga_beli' => 12000,
            'harga_jual' => 15000,
            'stok' => 10,
            'min_stok' => 1,
            'is_aktif' => '1',
        ])->assertSessionHasNoErrors();

        $barang = Barang::where('nama', 'Beras Premium')->firstOrFail();

        $hargaBeli = DaftarHarga::cariBeli(Supplier::umum()->id, $barang->id, 1);
        $this->assertNotNull($hargaBeli);
        $this->assertEquals(12000, (float) $hargaBeli->harga);

        $hargaJual = DaftarHarga::cariJual(Customer::umum()->id, $barang->id, 1);
        $this->assertNotNull($hargaJual);
        $this->assertEquals(15000, (float) $hargaJual->harga);
    }

    public function test_update_barang_memperbarui_harga_rekanan_umum(): void
    {
        $this->login();
        $barang = $this->makeBarang();

        $this->put(route('barang.update', $barang), [
            'nama' => $barang->nama,
            'satuan' => $barang->satuan,
            'harga_jual' => 9500,
            'harga_beli' => 3800,
            'harga_avg' => $barang->harga_avg,
            'stok' => $barang->stok,
            'min_stok' => $barang->min_stok,
            'keterangan' => '',
            'is_aktif' => '1',
        ])->assertSessionHasNoErrors();

        $hargaBeli = DaftarHarga::cariBeli(Supplier::umum()->id, $barang->id, 1);
        $this->assertNotNull($hargaBeli);
        $this->assertEquals(3800, (float) $hargaBeli->harga);

        $hargaJual = DaftarHarga::cariJual(Customer::umum()->id, $barang->id, 1);
        $this->assertNotNull($hargaJual);
        $this->assertEquals(9500, (float) $hargaJual->harga);
    }

    public function test_form_pembelian_mendefaultkan_supplier_umum(): void
    {
        $this->login();
        $umum = Supplier::umum();

        $this->get(route('pembelian.create'))
            ->assertOk()
            ->assertSee('Keranjang Pembelian')
            ->assertSee('value="'.$umum->id.'" selected', false);
    }

    public function test_form_penjualan_mendefaultkan_customer_umum(): void
    {
        $this->login();
        $umum = Customer::umum();

        $this->get(route('penjualan.create'))
            ->assertOk()
            ->assertSee('Keranjang Penjualan')
            ->assertSee('value="'.$umum->id.'" selected', false);
    }
}
