<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\Aset;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Rekening;
use App\Models\Supplier;
use App\Models\User;
use App\Services\JournalService;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeuanganIntegritasLanjutanTest extends TestCase
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

    private function akun(string $kode): int
    {
        return AkunPerkiraan::where('kode', $kode)->firstOrFail()->id;
    }

    private function makeBarang(int $stok = 100): array
    {
        $brg = Barang::create([
            'kode' => 'BRG-'.substr((string) uniqid(), -5),
            'nama' => 'Barang Integritas',
            'tipe' => 'barang',
            'stok' => $stok,
            'harga_beli' => 3000,
            'harga_jual' => 8000,
            'harga_avg' => 3000,
            'is_aktif' => true,
        ]);
        $cust = Customer::create(['kode' => 'CUST-'.substr((string) uniqid(), -5), 'nama' => 'Customer Integritas', 'is_aktif' => true]);
        $sup = Supplier::create(['kode' => 'SUP-'.substr((string) uniqid(), -5), 'nama' => 'Supplier Integritas', 'is_aktif' => true]);

        return [$brg, $cust, $sup];
    }

    private function makeRekeningKas(float $saldoAwal = 0): Rekening
    {
        return Rekening::create([
            'jenis' => 'kas',
            'nama' => 'Kas Integritas',
            'akun_id' => $this->akun('111'),
            'saldo_awal' => $saldoAwal,
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
            'diskon' => 0,
            'diskon_tipe' => 'nominal',
            'sync_harga' => null,
            'items' => [
                ['barang_id' => $barangId, 'jumlah' => $qty, 'harga_satuan' => $harga, 'diskon' => 0],
            ],
        ])->assertSessionHasNoErrors();

        return Pembelian::orderByDesc('id')->first();
    }

    private function arusKas(): array
    {
        return ReportService::arusKas(now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString());
    }

    // === Arus Kas: PPN sebagai modal kerja operasional ===

    public function test_arus_kas_ppn_keluaran_tercatat_di_operasional_dan_selisih_nol(): void
    {
        $this->login();

        // Kas masuk penjualan + PPN: DR kas 111.000, CR penjualan 100.000, CR PPN Keluaran 11.000
        JournalService::post('kas_masuk', now()->toDateString(), [
            ['akun_id' => $this->akun('111'), 'debit' => 111000, 'kredit' => 0],
            ['akun_id' => $this->akun('411'), 'debit' => 0, 'kredit' => 100000],
            ['akun_id' => $this->akun('212'), 'debit' => 0, 'kredit' => 11000],
        ], 'Penjualan tunai + PPN');

        $data = $this->arusKas();

        $this->assertEqualsWithDelta(11000, (float) $data['perubahan_ppn_keluaran'], 0.01);
        $this->assertEqualsWithDelta(111000, (float) $data['kas_operasional'], 0.01);
        $this->assertEqualsWithDelta(111000, (float) $data['kas_akhir'], 0.01);
        $this->assertEqualsWithDelta(0, (float) $data['selisih'], 0.01);
    }

    public function test_arus_kas_ppn_masukan_tercatat_di_operasional_dan_selisih_nol(): void
    {
        $this->login();

        // Kas keluar beban + PPN: DR beban 90.000, DR PPN Masukan 9.900, CR kas 99.900
        JournalService::post('kas_keluar', now()->toDateString(), [
            ['akun_id' => $this->akun('523'), 'debit' => 90000, 'kredit' => 0],
            ['akun_id' => $this->akun('213'), 'debit' => 9900, 'kredit' => 0],
            ['akun_id' => $this->akun('111'), 'debit' => 0, 'kredit' => 99900],
        ], 'Bayar operasional + PPN');

        $data = $this->arusKas();

        $this->assertEqualsWithDelta(9900, (float) $data['perubahan_ppn_masukan'], 0.01);
        $this->assertEqualsWithDelta(-99900, (float) $data['kas_operasional'], 0.01);
        $this->assertEqualsWithDelta(0, (float) $data['selisih'], 0.01);
    }

    // === Arus Kas: disposisi aset hanya menghitung kas riil ===

    public function test_arus_kas_penjualan_aset_menyusut_selisih_nol_dan_investasi_hanya_proceeds(): void
    {
        $this->login();
        $aset = Aset::factory()->garisLurus(300000, 3, now()->startOfMonth()->subMonth()->format('Y-m-d'))->aktif()->create();

        $this->post(route('penyusutan.proses'), ['periode' => now()->format('Y-m')])->assertSessionHasNoErrors();
        $this->post(route('aset.disposisi', $aset), ['alasan' => 'dijual', 'harga_jual' => 250000])
            ->assertSessionHasNoErrors();

        $data = $this->arusKas();

        // Laba penjualan dikeluarkan dari operasional; investasi hanya hasil kas riil.
        $this->assertEqualsWithDelta(50000, (float) $data['laba_disposisi'], 0.01);
        $this->assertEqualsWithDelta(100000, (float) $data['akumulasi_disposisi'], 0.01);
        $this->assertEqualsWithDelta(250000, (float) $data['kas_investasi'], 0.01);
        $this->assertEqualsWithDelta(250000, (float) $data['kas_akhir'], 0.01);
        $this->assertEqualsWithDelta(0, (float) $data['selisih'], 0.01);
    }

    public function test_arus_kas_penghapusan_aset_rusak_tanpa_kas_selisih_nol(): void
    {
        $this->login();
        $aset = Aset::factory()->garisLurus(300000, 3, now()->startOfMonth()->subMonth()->format('Y-m-d'))->aktif()->create();

        $this->post(route('penyusutan.proses'), ['periode' => now()->format('Y-m')])->assertSessionHasNoErrors();
        $this->post(route('aset.disposisi', $aset), ['alasan' => 'rusak'])->assertSessionHasNoErrors();

        $data = $this->arusKas();

        $this->assertEqualsWithDelta(-200000, (float) $data['laba_disposisi'], 0.01);
        $this->assertEqualsWithDelta(0, (float) $data['kas_investasi'], 0.01);
        $this->assertEqualsWithDelta(0, (float) $data['kas_akhir'], 0.01);
        $this->assertEqualsWithDelta(0, (float) $data['selisih'], 0.01);
    }

    // === Void penjualan kredit berbayar: blokir ===

    public function test_void_penjualan_kredit_yang_sudah_dilunasi_ditolak(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);
        $penjualan = $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas();

        $this->post(route('penjualan.pelunasan', $penjualan), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'nominal' => 36000,
            'keterangan' => 'Lunas',
        ])->assertSessionHasNoErrors();

        $this->post(route('penjualan.void', $penjualan))->assertSessionHas('error');

        $this->assertEquals('posted', $penjualan->fresh()->status);
        $this->assertLessThanOrEqual(0.005, (float) $penjualan->fresh()->sisa_piutang);
    }

    public function test_void_penjualan_kredit_belum_dibayar_masih_berhasil(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);
        $penjualan = $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);

        $this->post(route('penjualan.void', $penjualan))->assertSessionHasNoErrors();

        $this->assertEquals('draft', $penjualan->fresh()->status);
    }

    // === Kas Masuk: cap & wajib customer pada pelunasan piutang ===

    public function test_kas_masuk_pelunasan_piutang_wajib_customer(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);
        $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas();

        $this->post(route('kas-masuk.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'keterangan' => 'Tanpa customer',
            'items' => [['akun_id' => $this->akun('113'), 'keterangan' => 'Pelunasan', 'nominal' => 10000]],
        ])->assertSessionHas('error');

        $this->assertSame(0, KasMasuk::count());
    }

    public function test_kas_masuk_pelunasan_melebihi_sisa_piutang_ditolak(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);
        $penjualan = $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas();

        $this->post(route('kas-masuk.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'customer_id' => $cust->id,
            'keterangan' => 'Over bayar',
            'items' => [['akun_id' => $this->akun('113'), 'keterangan' => 'Pelunasan', 'nominal' => 50000]],
        ])->assertSessionHas('error');

        $this->assertSame(0, KasMasuk::count());
        $this->assertEqualsWithDelta(36000, (float) $penjualan->fresh()->sisa_piutang, 0.01);
    }

    public function test_kas_masuk_pelunasan_maksimal_sama_dengan_sisa_berhasil(): void
    {
        $this->login();
        [$brg, $cust] = $this->makeBarang(10);
        $penjualan = $this->postPenjualanKredit($cust->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas();

        $this->post(route('kas-masuk.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'customer_id' => $cust->id,
            'keterangan' => 'Lunas penuh',
            'items' => [['akun_id' => $this->akun('113'), 'keterangan' => 'Pelunasan', 'nominal' => 36000]],
        ])->assertSessionHas('success');

        $this->assertSame(1, KasMasuk::count());
        $this->assertLessThanOrEqual(0.005, (float) $penjualan->fresh()->sisa_piutang);
    }

    // === Kas Keluar: cap & wajib supplier pada pembayaran hutang ===

    public function test_kas_keluar_pembayaran_hutang_wajib_supplier(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(10);
        $this->postPembelianKredit($sup->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas();

        $this->post(route('kas-keluar.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'keterangan' => 'Tanpa supplier',
            'items' => [['akun_id' => $this->akun('211'), 'keterangan' => 'Bayar hutang', 'nominal' => 10000]],
        ])->assertSessionHas('error');

        $this->assertSame(0, KasKeluar::count());
    }

    public function test_kas_keluar_pembayaran_melebihi_sisa_hutang_ditolak(): void
    {
        $this->login();
        [$brg, $sup] = $this->makeBarang(10);
        $pembelian = $this->postPembelianKredit($sup->id, $brg->id, 4, 9000);
        $rekening = $this->makeRekeningKas(1_000_000);

        $this->post(route('kas-keluar.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'supplier_id' => $sup->id,
            'keterangan' => 'Over bayar',
            'items' => [['akun_id' => $this->akun('211'), 'keterangan' => 'Bayar hutang', 'nominal' => 50000]],
        ])->assertSessionHas('error');

        $this->assertSame(0, KasKeluar::count());
        $this->assertEqualsWithDelta(36000, (float) $pembelian->fresh()->sisaHutang, 0.01);
    }

    // === Rekening yang terhubung jurnal tidak bisa dihapus ===

    public function test_hapus_rekening_yang_memiliki_jurnal_saldo_awal_ditolak(): void
    {
        $this->login();

        $this->post(route('rekening.store'), [
            'jenis' => 'bank',
            'nama' => 'Bank BCA',
            'saldo_awal' => 250000,
            'is_aktif' => true,
        ])->assertSessionHas('success');

        $rekening = Rekening::where('nama', 'Bank BCA')->firstOrFail();
        $this->assertNotNull($rekening->akun);

        $this->delete(route('rekening.destroy', $rekening))->assertSessionHas('error');

        $this->assertDatabaseHas('rekenings', ['id' => $rekening->id]);
    }

    public function test_hapus_rekening_bersih_tanpa_jurnal_berhasil(): void
    {
        $this->login();
        $rekening = $this->makeRekeningKas();

        $this->delete(route('rekening.destroy', $rekening))->assertSessionHas('success');

        $this->assertDatabaseMissing('rekenings', ['id' => $rekening->id]);
    }
}
