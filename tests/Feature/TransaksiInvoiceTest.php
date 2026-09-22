<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\DaftarHarga;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiInvoiceTest extends TestCase
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

    private function makeBarang(): Barang
    {
        $barang = Barang::where('tipe', 'barang')->first();
        if (! $barang) {
            $barang = Barang::create([
                'kode' => 'BRG-PDF',
                'nama' => 'Barang PDF',
                'tipe' => 'barang',
                'stok' => 100,
                'harga_beli' => 5000,
                'harga_jual' => 8000,
                'is_aktif' => true,
            ]);
        }

        return $barang;
    }

    public function test_pembelian_invoice_pdf()
    {
        $this->login();
        $sup = Supplier::create(['kode' => 'SUP-PDF', 'nama' => 'Supplier PDF', 'is_aktif' => true]);
        $brg = $this->makeBarang();

        $this->post(route('pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $sup->id,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 5, 'harga_satuan' => 5000, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        $pembelian = Pembelian::orderByDesc('id')->first();
        $this->assertNotNull($pembelian);

        $r = $this->get(route('pembelian.pdf', $pembelian));
        $r->assertStatus(200);
        $r->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_penjualan_invoice_pdf()
    {
        $this->login();
        $cust = Customer::create(['kode' => 'CUST-PDF', 'nama' => 'Customer PDF', 'is_aktif' => true]);
        $brg = $this->makeBarang();

        $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $cust->id,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 2, 'harga_satuan' => 8000, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        $penjualan = Penjualan::orderByDesc('id')->first();
        $this->assertNotNull($penjualan);

        $r = $this->get(route('penjualan.pdf', $penjualan));
        $r->assertStatus(200);
        $r->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_create_pages_render_with_mixed_entitas_pricing()
    {
        $this->login();
        $sup = Supplier::create(['kode' => 'SUP-MIX', 'nama' => 'Supplier Campur', 'is_aktif' => true]);
        $cust = Customer::create(['kode' => 'CUST-MIX', 'nama' => 'Customer Campur', 'is_aktif' => true]);
        $brg = $this->makeBarang();

        // Harga beli (supplier) dan harga jual (customer) untuk barang yang sama
        DaftarHarga::create(['entitas' => 'supplier', 'supplier_id' => $sup->id, 'barang_id' => $brg->id, 'harga' => 5000, 'min_qty' => 1, 'max_qty' => null, 'is_aktif' => true]);
        DaftarHarga::create(['entitas' => 'customer', 'customer_id' => $cust->id, 'barang_id' => $brg->id, 'harga' => 9000, 'min_qty' => 1, 'max_qty' => null, 'is_aktif' => true]);

        $this->get(route('pembelian.create'))->assertStatus(200);
        $this->get(route('penjualan.create'))->assertStatus(200);
    }
}
