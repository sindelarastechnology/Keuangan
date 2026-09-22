<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\Barang;
use App\Models\BbHutang;
use App\Models\Customer;
use App\Models\JurnalItem;
use App\Models\Pajak;
use App\Models\Rekening;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OngkirTransaksiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::where('email', 'admin@keuangan.test')->first());
    }

    private function akunKode(string $kode): int
    {
        return (int) AkunPerkiraan::where('kode', $kode)->value('id');
    }

    private function akunBebanTransport(): int
    {
        return (int) AkunPerkiraan::where('kode', '524')->value('id');
    }

    private function makeBarang(bool $jasa = false): Barang
    {
        return Barang::create([
            'kode' => 'BRG-ONG-'.substr((string) uniqid(), -5),
            'nama' => $jasa ? 'Jasa Ongkir' : 'Barang Ongkir',
            'tipe' => $jasa ? 'jasa' : 'barang',
            'stok' => $jasa ? 0 : 10,
            'harga_beli' => 3000,
            'harga_jual' => 9000,
            'is_aktif' => true,
        ]);
    }

    private function makeRekeningKas(): Rekening
    {
        return Rekening::create([
            'jenis' => 'kas',
            'nama' => 'Kas Ongkir',
            'nomor_rekening' => null,
            'nama_pemilik' => null,
            'akun_id' => $this->akunKode('111'),
            'saldo_awal' => 0,
            'is_aktif' => true,
        ]);
    }

    public function test_halaman_create_pembelian_menampilkan_galeri_dan_input_ongkir(): void
    {
        $this->get(route('pembelian.create'))
            ->assertOk()
            ->assertViewHas('galeri')
            ->assertSee('Pilih Item')
            ->assertSee('Keranjang Pembelian')
            ->assertSee('ongkir_input');
    }

    public function test_halaman_create_penjualan_menampilkan_galeri_dan_input_ongkir(): void
    {
        $this->get(route('penjualan.create'))
            ->assertOk()
            ->assertViewHas('galeri')
            ->assertSee('Pilih Item')
            ->assertSee('Keranjang Penjualan')
            ->assertSee('ongkir_input');
    }

    public function test_ongkir_pembelian_masuk_total_dan_jurnal_beban_transport(): void
    {
        $sup = Supplier::create(['kode' => 'SUP-ONG-'.substr((string) uniqid(), -5), 'nama' => 'Supplier Ongkir', 'is_aktif' => true]);
        $brg = $this->makeBarang();
        $pajak = Pajak::create(['nama' => 'PPN 11%', 'rate' => 11, 'is_aktif' => true]);

        $this->post(route('pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $sup->id,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'pajak_id' => $pajak->id,
            'ongkir' => 15000,
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 10, 'harga_satuan' => 3000, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors()->assertRedirect();

        $pembelian = DB::table('pembelians')->orderByDesc('id')->first();
        $this->assertEquals(30000, (float) $pembelian->subtotal);
        $this->assertEquals(3300, (float) $pembelian->pajak_nominal);
        $this->assertEquals(15000, (float) $pembelian->ongkir);
        $this->assertEquals(48300, (float) $pembelian->total);

        $debitBeban = (float) JurnalItem::where('akun_id', $this->akunBebanTransport())->sum('debit');
        $this->assertEquals(15000, $debitBeban);

        $this->assertEquals(48300, (float) BbHutang::orderByDesc('id')->value('saldo'));
    }

    public function test_ongkir_penjualan_masuk_total_dan_mengurangi_beban_transport(): void
    {
        $cust = Customer::create(['kode' => 'CUST-ONG-'.substr((string) uniqid(), -5), 'nama' => 'Customer Ongkir', 'is_aktif' => true]);
        $brg = $this->makeBarang();
        $rekening = $this->makeRekeningKas();

        $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $cust->id,
            'metode_bayar' => 'tunai',
            'rekening_id' => $rekening->id,
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'ongkir' => 10000,
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 5, 'harga_satuan' => 9000, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors()->assertRedirect();

        $penjualan = DB::table('penjualans')->orderByDesc('id')->first();
        $this->assertEquals(45000, (float) $penjualan->subtotal);
        $this->assertEquals(10000, (float) $penjualan->ongkir);
        $this->assertEquals(55000, (float) $penjualan->total);

        $kreditBeban = (float) JurnalItem::where('akun_id', $this->akunBebanTransport())->sum('kredit');
        $this->assertEquals(10000, $kreditBeban);
    }

    public function test_jurnal_penjualan_ongkir_selalu_seimbang(): void
    {
        $cust = Customer::create(['kode' => 'CUST-ONG2-'.substr((string) uniqid(), -5), 'nama' => 'Customer Ongkir 2', 'is_aktif' => true]);
        $brg = $this->makeBarang();
        $rekening = $this->makeRekeningKas();

        $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $cust->id,
            'metode_bayar' => 'tunai',
            'rekening_id' => $rekening->id,
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'ongkir' => 10000,
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 2, 'harga_satuan' => 9000, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors()->assertRedirect();

        $penjualanId = DB::table('penjualans')->orderByDesc('id')->value('id');
        $ep = DB::table('jurnal_umum')->where('ref_type', 'App\Models\Penjualan')->where('ref_id', $penjualanId)->get();

        $this->assertNotEmpty($ep);
        foreach ($ep as $jurnal) {
            $debit = (float) JurnalItem::where('jurnal_id', $jurnal->id)->sum('debit');
            $kredit = (float) JurnalItem::where('jurnal_id', $jurnal->id)->sum('kredit');
            $this->assertEqualsWithDelta($debit, $kredit, 0.01);
        }
    }

    public function test_ongkir_tidak_boleh_negatif(): void
    {
        $sup = Supplier::create(['kode' => 'SUP-ONG-'.substr((string) uniqid(), -5), 'nama' => 'Supplier Ongkir Neg', 'is_aktif' => true]);
        $brg = $this->makeBarang();

        $this->post(route('pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $sup->id,
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'ongkir' => -1000,
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 1, 'harga_satuan' => 3000, 'diskon' => 0],
            ],
        ])->assertSessionHasErrors('ongkir');
    }
}
