<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\Gudang;
use App\Models\Pembelian;
use App\Models\Pengaturan;
use App\Models\Penjualan;
use App\Models\StokGudang;
use App\Models\Supplier;
use App\Models\User;
use App\Services\PengaturanSistemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PengaturanSistemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::where('email', 'admin@keuangan.test')->firstOrFail());
    }

    private function idKode(string $kode): int
    {
        return (int) AkunPerkiraan::where('kode', $kode)->value('id');
    }

    private function buatCabang(): Gudang
    {
        return Gudang::create(['kode' => 'GDG-0002', 'nama' => 'Gudang Cabang', 'is_aktif' => true]);
    }

    private function buatBarang(float $stok = 0): Barang
    {
        return Barang::create([
            'kode' => 'BRG-9001',
            'nama' => 'Produk Gudang',
            'tipe' => 'barang',
            'satuan' => 'pcs',
            'stok' => $stok,
            'harga_beli' => 3000,
            'harga_jual' => 9000,
            'harga_avg' => 3000,
            'is_aktif' => true,
        ]);
    }

    private function beli(int $supplierId, int $barangId, float $qty, ?int $gudangId = null): TestResponse
    {
        return $this->post(route('pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $supplierId,
            'gudang_id' => $gudangId,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => 3000, 'diskon' => 0]],
        ]);
    }

    private function jual(int $customerId, int $barangId, float $qty, ?int $gudangId = null): TestResponse
    {
        return $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $customerId,
            'gudang_id' => $gudangId,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => 9000, 'diskon' => 0]],
        ]);
    }

    private function stokDiGudang(int $barangId, int $gudangId): float
    {
        return (float) (StokGudang::where('barang_id', $barangId)->where('gudang_id', $gudangId)->value('qty') ?? 0);
    }

    // ===== Pengaturan Sistem: Akun Penting =====

    public function test_halaman_pengaturan_sistem_menampilkan_tab_akun_dan_gudang(): void
    {
        $this->get(route('pengaturan.sistem.index'))
            ->assertOk()
            ->assertSee('Akun Penting')
            ->assertSee('Pengaturan Gudang')
            ->assertSee('Hutang Usaha');
    }

    public function test_menyimpan_akun_penting_mengubah_resolusi_akun(): void
    {
        $akun421 = $this->idKode('421');

        $this->post(route('pengaturan.sistem.update'), [
            'tab' => 'akun',
            'akun_penjualan' => $akun421,
        ])->assertSessionHasNoErrors()->assertRedirect(route('pengaturan.sistem.index', ['tab' => 'akun']));

        $this->assertDatabaseHas('pengaturan', ['key' => 'akun_penjualan', 'value' => (string) $akun421]);
        $this->assertEquals($akun421, PengaturanSistemService::akunId('penjualan'));
    }

    public function test_akun_penting_tidak_valid_ditolak(): void
    {
        $this->post(route('pengaturan.sistem.update'), [
            'tab' => 'akun',
            'akun_piutang' => 999999,
        ])->assertSessionHasErrors('akun_piutang');

        $this->assertDatabaseMissing('pengaturan', ['key' => 'akun_piutang']);
    }

    public function test_kosongkan_akun_penting_menghapus_pengaturan_dan_kembali_ke_default(): void
    {
        $akun114 = $this->idKode('114');
        Pengaturan::atur('akun_persediaan', (string) $akun114);
        $this->assertEquals($akun114, PengaturanSistemService::akunId('persediaan'));

        $this->post(route('pengaturan.sistem.update'), [
            'tab' => 'akun',
            'akun_persediaan' => '',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('pengaturan', ['key' => 'akun_persediaan']);
        $this->assertEquals($this->idKode('115'), PengaturanSistemService::akunId('persediaan'));
    }

    public function test_menyimpan_gudang_default_pembelian_dan_penjualan(): void
    {
        $cabang = $this->buatCabang();

        $this->post(route('pengaturan.sistem.update'), [
            'tab' => 'gudang',
            'gudang_default_pembelian' => $cabang->id,
            'gudang_default_penjualan' => $cabang->id,
        ])->assertSessionHasNoErrors()->assertRedirect(route('pengaturan.sistem.index', ['tab' => 'gudang']));

        $this->assertDatabaseHas('pengaturan', ['key' => 'gudang_default_pembelian', 'value' => (string) $cabang->id]);
        $this->assertEquals($cabang->id, PengaturanSistemService::gudangPembelian());
        $this->assertEquals($cabang->id, PengaturanSistemService::gudangPenjualan());
    }

    public function test_gudang_default_tidak_valid_ditolak(): void
    {
        $this->post(route('pengaturan.sistem.update'), [
            'tab' => 'gudang',
            'gudang_default_penjualan' => 999999,
        ])->assertSessionHasErrors('gudang_default_penjualan');

        $this->assertDatabaseMissing('pengaturan', ['key' => 'gudang_default_penjualan']);
    }

    // ===== Alur stok per gudang transaksi =====

    public function test_pembelian_masuk_gudang_tujuan_dan_bukan_gudang_utama(): void
    {
        $utama = Gudang::utama();
        $cabang = $this->buatCabang();
        $brg = $this->buatBarang();
        $sup = Supplier::create(['kode' => 'SUP-G1', 'nama' => 'Supplier G1', 'is_aktif' => true]);

        $this->beli($sup->id, $brg->id, 10, $cabang->id)->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(10, (float) $brg->stok);
        $this->assertEquals(10, $this->stokDiGudang($brg->id, $cabang->id));
        $this->assertEquals(0, $this->stokDiGudang($brg->id, $utama->id));
        $this->assertEquals((float) $brg->stok, StokGudang::where('barang_id', $brg->id)->sum('qty'));
    }

    public function test_pembelian_tanpa_gudang_masuk_gudang_utama(): void
    {
        $utama = Gudang::utama();
        $brg = $this->buatBarang();
        $sup = Supplier::create(['kode' => 'SUP-G2', 'nama' => 'Supplier G2', 'is_aktif' => true]);

        $this->beli($sup->id, $brg->id, 5)->assertSessionHasNoErrors();

        $this->assertEquals(5, $this->stokDiGudang($brg->id, $utama->id));
        $this->assertEquals((float) $brg->fresh()->stok, StokGudang::where('barang_id', $brg->id)->sum('qty'));
    }

    public function test_penjualan_mengurangi_gudang_terpilih(): void
    {
        $cabang = $this->buatCabang();
        $brg = $this->buatBarang();
        $sup = Supplier::create(['kode' => 'SUP-G3', 'nama' => 'Supplier G3', 'is_aktif' => true]);
        $cust = Customer::create(['kode' => 'CUST-G1', 'nama' => 'Customer G1', 'is_aktif' => true]);

        $this->beli($sup->id, $brg->id, 10, $cabang->id)->assertSessionHasNoErrors();
        $this->jual($cust->id, $brg->id, 4, $cabang->id)->assertSessionHasNoErrors();

        $brg->refresh();
        $this->assertEquals(6, (float) $brg->stok);
        $this->assertEquals(6, $this->stokDiGudang($brg->id, $cabang->id));
        $this->assertEquals((float) $brg->stok, StokGudang::where('barang_id', $brg->id)->sum('qty'));
    }

    public function test_penjualan_ditolak_saat_stok_gudang_tidak_cukup_walaupun_gudang_lain_memiliki_stok(): void
    {
        $utama = Gudang::utama();
        $cabang = $this->buatCabang();
        $brg = $this->buatBarang();
        $sup = Supplier::create(['kode' => 'SUP-G4', 'nama' => 'Supplier G4', 'is_aktif' => true]);
        $cust = Customer::create(['kode' => 'CUST-G2', 'nama' => 'Customer G2', 'is_aktif' => true]);

        $this->beli($sup->id, $brg->id, 10, $cabang->id)->assertSessionHasNoErrors();

        // Total stok 10 cukup, tapi gudang utama (gudang terpilih) kosong (0).
        $this->jual($cust->id, $brg->id, 3, $utama->id)->assertSessionHasErrors('items');

        $this->assertEquals(10, (float) $brg->fresh()->stok);
        $this->assertEquals(0, $this->stokDiGudang($brg->id, $utama->id));
        $this->assertEquals(10, $this->stokDiGudang($brg->id, $cabang->id));
    }

    public function test_penjualan_per_gudang_tidak_menembus_batas_stok_gudang(): void
    {
        $cabang = $this->buatCabang();
        $brg = $this->buatBarang();
        $sup = Supplier::create(['kode' => 'SUP-G5', 'nama' => 'Supplier G5', 'is_aktif' => true]);
        $cust = Customer::create(['kode' => 'CUST-G3', 'nama' => 'Customer G3', 'is_aktif' => true]);

        $this->beli($sup->id, $brg->id, 5, $cabang->id)->assertSessionHasNoErrors();

        $this->jual($cust->id, $brg->id, 5, $cabang->id)->assertSessionHasNoErrors();
        $this->assertEquals(0, (float) $brg->fresh()->stok);

        $this->jual($cust->id, $brg->id, 1, $cabang->id)->assertSessionHasErrors('items');

        $this->assertEquals(0, (float) $brg->fresh()->stok);
        $this->assertEquals(0, $this->stokDiGudang($brg->id, $cabang->id));
    }

    public function test_show_pembelian_dan_penjualan_menampilkan_gudang(): void
    {
        $cabang = $this->buatCabang();
        $brg = $this->buatBarang();
        $sup = Supplier::create(['kode' => 'SUP-G6', 'nama' => 'Supplier G6', 'is_aktif' => true]);
        $cust = Customer::create(['kode' => 'CUST-G4', 'nama' => 'Customer G4', 'is_aktif' => true]);

        $this->beli($sup->id, $brg->id, 5, $cabang->id)->assertSessionHasNoErrors();
        $this->jual($cust->id, $brg->id, 2, $cabang->id)->assertSessionHasNoErrors();

        $this->get(route('pembelian.show', app(Pembelian::class)->first()))
            ->assertOk()
            ->assertSee('Gudang Cabang');
        $this->get(route('penjualan.show', app(Penjualan::class)->first()))
            ->assertOk()
            ->assertSee('Gudang Cabang');
    }
}
