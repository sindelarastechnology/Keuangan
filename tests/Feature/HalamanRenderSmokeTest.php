<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HalamanRenderSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::where('email', 'admin@keuangan.test')->firstOrFail());
    }

    public function test_semua_halaman_tanpa_parameter_merender_ok(): void
    {
        $routes = [
            'dashboard',
            'akun-perkiraan.index',
            'akun-perkiraan.create',
            'aset.index',
            'aset.create',
            'barang.index',
            'barang.create',
            'bb-piutang.index',
            'bb-hutang.index',
            'bb-persediaan.index',
            'buku-besar.index',
            'customer.index',
            'customer.create',
            'daftar-harga.index',
            'daftar-harga.create',
            'daftar-harga.riwayat',
            'donasi.index',
            'data-nama.index',
            'data-nama.create',
            'data-produk.index',
            'data-produk.create',
            'gudang.index',
            'gudang.create',
            'jasa.index',
            'jasa.create',
            'jurnal.index',
            'jurnal.create',
            'kas-masuk.index',
            'kas-masuk.create',
            'kas-keluar.index',
            'kas-keluar.create',
            'laporan.laba-rugi',
            'laporan.neraca',
            'laporan.neraca-saldo',
            'laporan.arus-kas',
            'laporan.penyusutan',
            'mutasi-bank.index',
            'mutasi-bank.create',
            'pajak.index',
            'pajak.create',
            'pembelian.index',
            'pembelian.create',
            'pengaturan.index',
            'penjualan.index',
            'penjualan.create',
            'penyusutan.index',
            'penyusutan.kalkulator',
            'periode.index',
            'perubahan-stok.index',
            'perubahan-stok.create',
            'profile.edit',
            'rekening.index',
            'rekening.create',
            'retur-pembelian.index',
            'retur-pembelian.create',
            'retur-penjualan.index',
            'retur-penjualan.create',
            'stok-opname.index',
            'stok-opname.create',
            'supplier.index',
            'supplier.create',
            'tagihan.hutang',
            'tagihan.piutang',
            'transfer-gudang.index',
            'transfer-gudang.create',
            'tutup-buku.index',
            'tutup-buku-tahunan.index',
        ];

        foreach ($routes as $route) {
            $response = $this->get(route($route));
            $response->assertStatus(200, "Halaman {$route} gagal: ".$response->getStatusCode());
        }

        $this->assertTrue(true);
    }
}
