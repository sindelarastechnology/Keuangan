<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\Barang;
use App\Models\BbPiutang;
use App\Models\Customer;
use App\Models\KasMasuk;
use App\Models\Penjualan;
use App\Models\Rekening;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PiutangPelunasanTest extends TestCase
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

    private function makeBarang(int $stok = 100): array
    {
        $brg = Barang::create([
            'kode' => 'BRG-'.substr((string) uniqid(), -5),
            'nama' => 'Barang Piutang',
            'tipe' => 'barang',
            'stok' => $stok,
            'harga_beli' => 3000,
            'harga_jual' => 8000,
            'harga_avg' => 3000,
            'is_aktif' => true,
        ]);
        $cust = Customer::create(['kode' => 'CUST-'.substr((string) uniqid(), -5), 'nama' => 'Customer Piutang', 'is_aktif' => true]);

        return [$brg, $cust];
    }

    private function makeRekeningKas(): Rekening
    {
        return Rekening::create([
            'jenis' => 'kas',
            'nama' => 'Kas Pelunasan',
            'akun_id' => AkunPerkiraan::where('kode', '111')->value('id'),
            'saldo_awal' => 0,
            'is_aktif' => true,
        ]);
    }

    private function postPenjualanKredit(int $customerId, int $barangId, float $qty, float $harga, string $metode = 'kredit', ?int $rekeningId = null): Penjualan
    {
        $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $customerId,
            'aksi' => 'posted',
            'metode_bayar' => $metode,
            'rekening_id' => $rekeningId,
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [
                ['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => $harga, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        return Penjualan::orderByDesc('id')->first();
    }

    private function pelunasan(Penjualan $penjualan, int $rekeningId, float $nominal)
    {
        return $this->post(route('penjualan.pelunasan', $penjualan), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekeningId,
            'nominal' => $nominal,
            'keterangan' => 'Test pelunasan',
        ]);
    }

    public function test_penjualan_kredit_posting_membuat_bb_piutang_teratribusi_ke_faktur(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);

        $penjualan = $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);

        $this->assertEquals(36000, (float) $penjualan->sisa_piutang);
        $this->assertTrue($penjualan->piutang_terverifikasi);

        $bb = BbPiutang::where('penjualan_id', $penjualan->id)->firstOrFail();
        $this->assertEquals($cust->id, $bb->customer_id);
        $this->assertEquals(36000, (float) $bb->debit);
        $this->assertEquals(0, (float) $bb->kredit);
        $this->assertEquals(36000, (float) $bb->saldo);
    }

    public function test_pelunasan_sebagian_mengurangi_sisa_dan_membuat_kas_masuk_seimbang(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);
        $penjualan = $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas();

        $this->pelunasan($penjualan, $rekening->id, 12000)
            ->assertRedirect(route('penjualan.show', $penjualan))
            ->assertSessionHas('success');

        $penjualan->refresh();
        $this->assertEqualsWithDelta(24000, (float) $penjualan->sisa_piutang, 0.01);

        $kasMasuk = KasMasuk::firstOrFail();
        $this->assertNotNull($kasMasuk->customer_id);
        $this->assertEquals(12000, (float) $kasMasuk->grand_total);
        $this->assertEquals(12000, (float) $kasMasuk->jurnal->items->sum('debit'));
        $this->assertEquals(12000, (float) $kasMasuk->jurnal->items->sum('kredit'));

        $last = BbPiutang::where('customer_id', $cust->id)->orderByDesc('id')->first();
        $this->assertEquals($penjualan->id, $last->penjualan_id);
        $this->assertEquals(0, (float) $last->debit);
        $this->assertEquals(12000, (float) $last->kredit);
        $this->assertEqualsWithDelta(24000, (float) $last->saldo, 0.01);
    }

    public function test_pelunasan_penuh_menjadikan_lunas_dan_menolak_pelunasan_lagi(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);
        $penjualan = $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas();

        $this->pelunasan($penjualan, $rekening->id, 36000)->assertSessionHas('success');

        $this->assertLessThanOrEqual(0.005, (float) $penjualan->fresh()->sisa_piutang);

        $this->get(route('penjualan.show', $penjualan))
            ->assertSee('Lunas')
            ->assertDontSee('Terima Pelunasan');

        $this->pelunasan($penjualan->fresh(), $rekening->id, 1000)
            ->assertSessionHas('error');
    }

    public function test_pelunasan_tidak_boleh_melebihi_sisa_tagihan(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);
        $penjualan = $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas();

        $this->pelunasan($penjualan, $rekening->id, 40000)
            ->assertSessionHasErrors('nominal');

        $this->assertEquals(0, KasMasuk::count());
        $this->assertGreaterThan(0.005, (float) $penjualan->fresh()->sisa_piutang);
    }

    public function test_pelunasan_hanya_untuk_kredit_yang_sudah_diposting(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);
        $rekening = $this->makeRekeningKas();

        $tunai = $this->postPenjualanKredit($cust->id, $brg->id, 2, 9000, 'tunai', $rekening->id);
        $this->pelunasan($tunai, $rekening->id, 18000)
            ->assertSessionHas('error');

        $this->post(route('penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'customer_id' => $cust->id,
            'aksi' => 'pending',
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 2, 'harga_satuan' => 9000, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        $draft = Penjualan::orderByDesc('id')->first();
        $this->pelunasan($draft, $rekening->id, 18000)
            ->assertSessionHas('error');

        $this->assertEquals(0, KasMasuk::count());
    }

    public function test_void_kas_masuk_pelunasan_mengembalikan_sisa_tagihan(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);
        $penjualan = $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas();

        $this->pelunasan($penjualan, $rekening->id, 24000)->assertSessionHas('success');
        $this->assertEqualsWithDelta(12000, (float) $penjualan->fresh()->sisa_piutang, 0.01);

        $kasMasuk = KasMasuk::firstOrFail();
        $this->delete(route('kas-masuk.destroy', $kasMasuk))->assertSessionHas('success');

        $this->assertEqualsWithDelta(36000, (float) $penjualan->fresh()->sisa_piutang, 0.01);
        $this->assertEqualsWithDelta(36000, (float) (BbPiutang::where('customer_id', $cust->id)->orderByDesc('id')->value('saldo') ?? 0), 0.01);
        $this->assertEquals(0, KasMasuk::count());
    }

    public function test_kas_masuk_umum_pelunasan_dialokasikan_fifo_ke_faktur_tertua(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(30);
        $a = $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);
        $b = $this->postPenjualanKredit($cust->id, $brg->id, 8, 9000);
        $rekening = $this->makeRekeningKas();
        $akunPiutang = AkunPerkiraan::where('kode', '113')->value('id');

        $this->post(route('kas-masuk.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'customer_id' => $cust->id,
            'keterangan' => 'Bayar umum',
            'items' => [['akun_id' => $akunPiutang, 'keterangan' => 'Pelunasan', 'nominal' => 50000]],
        ])->assertSessionHas('success');

        $this->assertEqualsWithDelta(0, (float) $a->fresh()->sisa_piutang, 0.01);
        $this->assertEqualsWithDelta(58000, (float) $b->fresh()->sisa_piutang, 0.01);
        $this->assertEqualsWithDelta(58000, (float) (BbPiutang::where('customer_id', $cust->id)->orderByDesc('id')->value('saldo') ?? 0), 0.01);
    }

    public function test_retur_penjualan_mengurangi_sisa_dan_void_retur_mengembalikan(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);
        $penjualan = $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);

        $itemId = $penjualan->items->first()->id;
        $this->post(route('retur-penjualan.store'), [
            'tanggal' => now()->toDateString(),
            'penjualan_id' => $penjualan->id,
            'items' => [['penjualan_item_id' => $itemId, 'jumlah' => 1]],
        ])->assertSessionHasNoErrors();

        $this->assertEqualsWithDelta(27000, (float) $penjualan->fresh()->sisa_piutang, 0.01);

        $returId = \DB::table('retur_penjualan')->orderByDesc('id')->value('id');
        $this->post(route('retur-penjualan.void', $returId))->assertSessionHas('success');

        $this->assertEqualsWithDelta(36000, (float) $penjualan->fresh()->sisa_piutang, 0.01);
    }
}
