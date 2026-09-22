<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\Barang;
use App\Models\BbHutang;
use App\Models\KasKeluar;
use App\Models\Pembelian;
use App\Models\Rekening;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HutangPelunasanTest extends TestCase
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
            'nama' => 'Barang Hutang',
            'tipe' => 'barang',
            'stok' => $stok,
            'harga_beli' => 3000,
            'harga_jual' => 8000,
            'harga_avg' => 3000,
            'is_aktif' => true,
        ]);
        $sup = Supplier::create(['kode' => 'SUP-'.substr((string) uniqid(), -5), 'nama' => 'Supplier Hutang', 'is_aktif' => true]);

        return [$brg, $sup];
    }

    private function makeRekeningKas(float $saldoAwal = 0): Rekening
    {
        return Rekening::create([
            'jenis' => 'kas',
            'nama' => 'Kas Pelunasan',
            'akun_id' => AkunPerkiraan::where('kode', '111')->value('id'),
            'saldo_awal' => $saldoAwal,
            'is_aktif' => true,
        ]);
    }

    private function postPembelianKredit(int $supplierId, int $barangId, float $qty, float $harga, string $metode = 'kredit', ?int $rekeningId = null): Pembelian
    {
        $this->post(route('pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $supplierId,
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

        return Pembelian::orderByDesc('id')->first();
    }

    private function pelunasan(Pembelian $pembelian, int $rekeningId, float $nominal)
    {
        return $this->post(route('pembelian.pelunasan', $pembelian), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekeningId,
            'nominal' => $nominal,
            'keterangan' => 'Test pelunasan',
        ]);
    }

    public function test_pembelian_kredit_posting_membuat_bb_hutang_teratribusi_ke_faktur(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(10);

        $pembelian = $this->postPembelianKredit($sup->id, $brg->id, 4, 9000);

        $this->assertEquals(36000, (float) $pembelian->sisaHutang);
        $this->assertTrue($pembelian->hutangTerverifikasi);

        $bb = BbHutang::where('pembelian_id', $pembelian->id)->firstOrFail();
        $this->assertEquals($sup->id, $bb->supplier_id);
        $this->assertEquals(0, (float) $bb->debit);
        $this->assertEquals(36000, (float) $bb->kredit);
        $this->assertEquals(36000, (float) $bb->saldo);
    }

    public function test_pelunasan_sebagian_mengurangi_sisa_dan_membuat_kas_keluar_seimbang(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(10);
        $pembelian = $this->postPembelianKredit($sup->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas();

        $this->pelunasan($pembelian, $rekening->id, 12000)
            ->assertRedirect(route('pembelian.show', $pembelian))
            ->assertSessionHas('success');

        $pembelian->refresh();
        $this->assertEqualsWithDelta(24000, (float) $pembelian->sisaHutang, 0.01);

        $kasKeluar = KasKeluar::firstOrFail();
        $this->assertNotNull($kasKeluar->supplier_id);
        $this->assertEquals(12000, (float) $kasKeluar->grand_total);
        $this->assertEquals(12000, (float) $kasKeluar->jurnal->items->sum('debit'));
        $this->assertEquals(12000, (float) $kasKeluar->jurnal->items->sum('kredit'));

        $last = BbHutang::where('supplier_id', $sup->id)->orderByDesc('id')->first();
        $this->assertEquals($pembelian->id, $last->pembelian_id);
        $this->assertEquals(12000, (float) $last->debit);
        $this->assertEquals(0, (float) $last->kredit);
        $this->assertEqualsWithDelta(24000, (float) $last->saldo, 0.01);
    }

    public function test_pelunasan_penuh_menjadikan_lunas_dan_menolak_pelunasan_lagi(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(10);
        $pembelian = $this->postPembelianKredit($sup->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas();

        $this->pelunasan($pembelian, $rekening->id, 36000)->assertSessionHas('success');

        $this->assertLessThanOrEqual(0.005, (float) $pembelian->fresh()->sisaHutang);

        $this->get(route('pembelian.show', $pembelian))
            ->assertSee('Lunas')
            ->assertDontSee('Catat Pelunasan');

        $this->pelunasan($pembelian->fresh(), $rekening->id, 1000)
            ->assertSessionHas('error');
    }

    public function test_pelunasan_tidak_boleh_melebihi_sisa_hutang(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(10);
        $pembelian = $this->postPembelianKredit($sup->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas();

        $this->pelunasan($pembelian, $rekening->id, 40000)
            ->assertSessionHasErrors('nominal');

        $this->assertEquals(0, KasKeluar::count());
        $this->assertGreaterThan(0.005, (float) $pembelian->fresh()->sisaHutang);
    }

    public function test_pelunasan_hanya_untuk_kredit_yang_sudah_diposting(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(10);
        $rekening = $this->makeRekeningKas();

        $tunai = $this->postPembelianKredit($sup->id, $brg->id, 2, 9000, 'tunai', $rekening->id);
        $this->pelunasan($tunai, $rekening->id, 18000)
            ->assertSessionHas('error');

        $this->post(route('pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'supplier_id' => $sup->id,
            'aksi' => 'pending',
            'metode_bayar' => 'kredit',
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'items' => [
                ['barang_id' => $brg->id, 'jumlah' => 2, 'harga_satuan' => 9000, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        $draft = Pembelian::orderByDesc('id')->first();
        $this->pelunasan($draft, $rekening->id, 18000)
            ->assertSessionHas('error');

        $this->assertEquals(0, KasKeluar::count());
    }

    public function test_void_kas_keluar_pelunasan_mengembalikan_sisa_hutang(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(10);
        $pembelian = $this->postPembelianKredit($sup->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas();

        $this->pelunasan($pembelian, $rekening->id, 24000)->assertSessionHas('success');
        $this->assertEqualsWithDelta(12000, (float) $pembelian->fresh()->sisaHutang, 0.01);

        $kasKeluar = KasKeluar::firstOrFail();
        $this->delete(route('kas-keluar.destroy', $kasKeluar))->assertSessionHas('success');

        $this->assertEqualsWithDelta(36000, (float) $pembelian->fresh()->sisaHutang, 0.01);
        $this->assertEqualsWithDelta(36000, (float) (BbHutang::where('supplier_id', $sup->id)->orderByDesc('id')->value('saldo') ?? 0), 0.01);
        $this->assertEquals(0, KasKeluar::count());
    }

    public function test_kas_keluar_umum_pelunasan_dialokasikan_fifo_ke_faktur_tertua(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(30);
        $a = $this->postPembelianKredit($sup->id, $brg->id, 4, 9000);
        $b = $this->postPembelianKredit($sup->id, $brg->id, 8, 9000);
        $rekening = $this->makeRekeningKas(1_000_000);
        $akunUtang = AkunPerkiraan::where('kode', '211')->value('id');

        $this->post(route('kas-keluar.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'supplier_id' => $sup->id,
            'keterangan' => 'Bayar umum',
            'items' => [['akun_id' => $akunUtang, 'keterangan' => 'Pelunasan', 'nominal' => 50000]],
        ])->assertSessionHas('success');

        $this->assertEqualsWithDelta(0, (float) $a->fresh()->sisaHutang, 0.01);
        $this->assertEqualsWithDelta(58000, (float) $b->fresh()->sisaHutang, 0.01);
        $this->assertEqualsWithDelta(58000, (float) (BbHutang::where('supplier_id', $sup->id)->orderByDesc('id')->value('saldo') ?? 0), 0.01);
    }

    public function test_retur_pembelian_mengurangi_sisa_dan_void_retur_mengembalikan(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(10);
        $pembelian = $this->postPembelianKredit($sup->id, $brg->id, 4, 9000);

        $itemId = $pembelian->items->first()->id;
        $this->post(route('retur-pembelian.store'), [
            'tanggal' => now()->toDateString(),
            'pembelian_id' => $pembelian->id,
            'items' => [['pembelian_item_id' => $itemId, 'jumlah' => 1]],
        ])->assertSessionHasNoErrors();

        $this->assertEqualsWithDelta(27000, (float) $pembelian->fresh()->sisaHutang, 0.01);

        $returId = \DB::table('retur_pembelian')->orderByDesc('id')->value('id');
        $this->post(route('retur-pembelian.void', $returId))->assertSessionHas('success');

        $this->assertEqualsWithDelta(36000, (float) $pembelian->fresh()->sisaHutang, 0.01);
    }
}
