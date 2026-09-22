<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\Barang;
use App\Models\BbHutang;
use App\Models\BbPiutang;
use App\Models\Customer;
use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Rekening;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagihanTest extends TestCase
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

    private function makeBarang(int $stok = 100, string $nama = 'Barang Tagihan'): array
    {
        $brg = Barang::create([
            'kode' => 'BRG-'.substr((string) uniqid(), -5),
            'nama' => $nama,
            'tipe' => 'barang',
            'stok' => $stok,
            'harga_beli' => 3000,
            'harga_jual' => 8000,
            'harga_avg' => 3000,
            'is_aktif' => true,
        ]);

        return [$brg];
    }

    private function makeCustomer(): Customer
    {
        return Customer::create(['kode' => 'CUST-'.substr((string) uniqid(), -5), 'nama' => 'Customer Tagihan', 'is_aktif' => true]);
    }

    private function makeSupplier(): Supplier
    {
        return Supplier::create(['kode' => 'SUP-'.substr((string) uniqid(), -5), 'nama' => 'Supplier Tagihan', 'is_aktif' => true]);
    }

    private function makeRekeningKas(): Rekening
    {
        return Rekening::create([
            'jenis' => 'kas',
            'nama' => 'Kas Tagihan',
            'akun_id' => AkunPerkiraan::where('kode', '111')->value('id'),
            'saldo_awal' => 0,
            'is_aktif' => true,
        ]);
    }

    private function postPenjualanKredit(int $customerId, int $barangId, float $qty, float $harga): Penjualan
    {
        $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $customerId,
            'aksi' => 'posted',
            'metode_bayar' => 'kredit',
            'rekening_id' => null,
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [
                ['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => $harga, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        return Penjualan::orderByDesc('id')->first();
    }

    private function postPembelianKredit(int $supplierId, int $barangId, float $qty, float $harga): Pembelian
    {
        $this->post(route('pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $supplierId,
            'aksi' => 'posted',
            'metode_bayar' => 'kredit',
            'rekening_id' => null,
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [
                ['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => $harga, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        return Pembelian::orderByDesc('id')->first();
    }

    public function test_hub_piutang_mencantumkan_customer_berbaki_dan_total_berjalan(): void
    {
        $this->login();
        [$brg] = $this->makeBarang();
        $cust = $this->makeCustomer();
        $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);

        $this->get(route('tagihan.piutang'))
            ->assertOk()
            ->assertSeeText($cust->nama)
            ->assertSeeText('Rp 36.000')
            ->assertSee('Terima Pembayaran');
    }

    public function test_hub_hutang_mencantumkan_supplier_berbaki(): void
    {
        $this->login();
        [$brg] = $this->makeBarang(20, 'Barang Hutang Tagihan');
        $sup = $this->makeSupplier();
        $this->postPembelianKredit($sup->id, $brg->id, 8, 5000);

        $this->get(route('tagihan.hutang'))
            ->assertOk()
            ->assertSeeText($sup->nama)
            ->assertSeeText('Rp 40.000')
            ->assertSee('Bayar Hutang');
    }

    public function test_bayar_piutang_dari_hub_dialokasikan_fifo_lintas_faktur(): void
    {
        $this->login();
        [$brg] = $this->makeBarang();
        $cust = $this->makeCustomer();
        $a = $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);
        $b = $this->postPenjualanKredit($cust->id, $brg->id, 8, 9000);
        $rekening = $this->makeRekeningKas();

        $this->post(route('tagihan.piutang.bayar.submit', $cust), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'nominal' => 50000,
            'keterangan' => 'Bayar dari hub',
        ])->assertRedirect(route('tagihan.piutang'))
            ->assertSessionHas('success');

        $this->assertEqualsWithDelta(0, (float) $a->fresh()->sisa_piutang, 0.01);
        $this->assertEqualsWithDelta(58000, (float) $b->fresh()->sisa_piutang, 0.01);

        $kasMasuk = KasMasuk::firstOrFail();
        $this->assertNotNull($kasMasuk->customer_id);
        $this->assertEquals(50000, (float) $kasMasuk->grand_total);
        $this->assertEquals(50000, (float) $kasMasuk->jurnal->items->sum('debit'));
        $this->assertEquals(50000, (float) $kasMasuk->jurnal->items->sum('kredit'));

        $last = BbPiutang::where('customer_id', $cust->id)->orderByDesc('id')->first();
        $this->assertEqualsWithDelta(58000, (float) $last->saldo, 0.01);
    }

    public function test_bayar_hutang_dari_hub_membuat_kas_keluar_dan_melunasi_faktur(): void
    {
        $this->login();
        [$brg] = $this->makeBarang(20, 'Barang Hutang Tagihan');
        $sup = $this->makeSupplier();
        $pembelian = $this->postPembelianKredit($sup->id, $brg->id, 8, 5000);
        $rekening = $this->makeRekeningKas();

        $this->post(route('tagihan.hutang.bayar.submit', $sup), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'nominal' => 40000,
        ])->assertRedirect(route('tagihan.hutang'))
            ->assertSessionHas('success');

        $this->assertLessThanOrEqual(0.005, (float) $pembelian->fresh()->sisaHutang);

        $kasKeluar = KasKeluar::firstOrFail();
        $this->assertNotNull($kasKeluar->supplier_id);
        $this->assertEquals(40000, (float) $kasKeluar->jurnal->items->sum('debit'));
        $this->assertEquals(40000, (float) $kasKeluar->jurnal->items->sum('kredit'));

        $last = BbHutang::where('supplier_id', $sup->id)->orderByDesc('id')->first();
        $this->assertEqualsWithDelta(0, (float) $last->saldo, 0.01);
    }

    public function test_bayar_piutang_ditolak_bila_melebihi_saldo_customer(): void
    {
        $this->login();
        [$brg] = $this->makeBarang();
        $cust = $this->makeCustomer();
        $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas();

        $this->post(route('tagihan.piutang.bayar.submit', $cust), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'nominal' => 60000,
        ])->assertSessionHasErrors('nominal');

        $this->assertEquals(0, KasMasuk::count());
        $this->assertEqualsWithDelta(36000, (float) $cust->fresh()->saldoPiutang, 0.01);
    }

    public function test_form_bayar_piutang_mempra_pilih_nominal_dari_faktur_yang_dikirim(): void
    {
        $this->login();
        [$brg] = $this->makeBarang();
        $cust = $this->makeCustomer();
        $a = $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);
        $this->postPenjualanKredit($cust->id, $brg->id, 8, 9000);

        $this->get(route('tagihan.piutang.bayar', ['customer' => $cust, 'faktur' => $a->id]))
            ->assertOk()
            ->assertSee('Dipilih')
            ->assertSee('36000.00');
    }

    public function test_customer_lunas_menghilang_dari_hub_dan_form_menolak(): void
    {
        $this->login();
        [$brg] = $this->makeBarang();
        $cust = $this->makeCustomer();
        $penjualan = $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas();

        $this->post(route('tagihan.piutang.bayar.submit', $cust), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'nominal' => 36000,
        ])->assertSessionHas('success');

        $this->get(route('dashboard'))->assertOk();

        $this->get(route('tagihan.piutang'))
            ->assertOk()
            ->assertDontSee('Terima Pembayaran');

        $this->get(route('tagihan.piutang.bayar', $cust))
            ->assertRedirect(route('tagihan.piutang'))
            ->assertSessionHas('error');

        $this->assertLessThanOrEqual(0.005, (float) $penjualan->fresh()->sisa_piutang);
    }

    public function test_daftar_penjualan_menampilkan_chip_sisa_dan_tombol_bayar_hanya_bila_ada_sisa(): void
    {
        $this->login();
        [$brg] = $this->makeBarang();
        $cust = $this->makeCustomer();
        $penjualan = $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas();

        $this->get(route('penjualan.index'))
            ->assertSee('Sisa Rp 36.000')
            ->assertSee(route('tagihan.piutang.bayar', ['customer' => $cust, 'faktur' => $penjualan->id]));

        $this->post(route('tagihan.piutang.bayar.submit', $cust), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'nominal' => 36000,
        ])->assertSessionHas('success');

        $this->get(route('penjualan.index'))
            ->assertDontSee('Sisa Rp 36.000')
            ->assertDontSee(route('tagihan.piutang.bayar', ['customer' => $cust, 'faktur' => $penjualan->id]));
    }

    public function test_pengguna_terdaftar_diperbolehkan_mengakses_hub_tagihan(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('tagihan.piutang'))->assertOk();
        $this->get(route('tagihan.hutang'))->assertOk();
    }
}
