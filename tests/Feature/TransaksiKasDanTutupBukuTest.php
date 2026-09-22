<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\BbHutang;
use App\Models\BbPiutang;
use App\Models\Customer;
use App\Models\JurnalUmum;
use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\MutasiBank;
use App\Models\PeriodeAkuntansi;
use App\Models\Rekening;
use App\Models\Supplier;
use App\Models\TutupBuku;
use App\Models\User;
use App\Services\JournalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiKasDanTutupBukuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function login(): User
    {
        $user = User::where('email', 'admin@keuangan.test')->firstOrFail();
        $this->actingAs($user);

        return $user;
    }

    private function akun(string $kode): AkunPerkiraan
    {
        return AkunPerkiraan::where('kode', $kode)->firstOrFail();
    }

    private function rekeningKas(float $saldoAwal = 0): Rekening
    {
        return Rekening::create([
            'jenis' => 'kas',
            'nama' => 'Kas Lokal',
            'akun_id' => $this->akun('111')->id,
            'saldo_awal' => $saldoAwal,
            'is_aktif' => true,
        ]);
    }

    private function rekeningBank(float $saldoAwal): Rekening
    {
        return Rekening::create([
            'jenis' => 'bank',
            'nama' => 'Bank Test',
            'akun_id' => $this->akun('112')->id,
            'saldo_awal' => $saldoAwal,
            'is_aktif' => true,
        ]);
    }

    public function test_kas_masuk_tersimpan_dengan_jurnal_seimbang(): void
    {
        $this->login();
        $rekening = $this->rekeningKas();
        $akunPenjualan = $this->akun('411');

        $response = $this->post(route('kas-masuk.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'keterangan' => 'Setoran penjualan tunai',
            'items' => [['akun_id' => $akunPenjualan->id, 'keterangan' => 'Penjualan', 'nominal' => 500000]],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $kasMasuk = KasMasuk::firstOrFail();
        $this->assertMatchesRegularExpression('#^KM/\d{2}/\d{4}/\d{4}$#', $kasMasuk->nomor);

        $jurnal = $kasMasuk->jurnal;
        $this->assertNotNull($jurnal);
        $this->assertEquals(500000, (float) $jurnal->items->sum('debit'));
        $this->assertEquals(500000, (float) $jurnal->items->sum('kredit'));
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $akunPenjualan->id && (float) $item->kredit === 500000.0));
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $rekening->akun_id && (float) $item->debit === 500000.0));
    }

    public function test_kas_masuk_pelunasan_piutang_tanpa_faktur_ditolak(): void
    {
        $this->login();
        $rekening = $this->rekeningKas();
        $customer = Customer::create(['kode' => 'C001', 'nama' => 'PT Contoh', 'is_aktif' => true]);
        $akunPiutang = $this->akun('113');

        $response = $this->post(route('kas-masuk.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'customer_id' => $customer->id,
            'keterangan' => 'Pelunasan piutang',
            'items' => [['akun_id' => $akunPiutang->id, 'keterangan' => 'Pelunasan', 'nominal' => 300000]],
        ]);

        // Pelunasan diblokir karena customer tidak memiliki piutang yang bisa dilunasi.
        $response->assertSessionHas('error');
        $this->assertSame(0, KasMasuk::count());
        $this->assertSame(0, BbPiutang::count());
    }

    public function test_kas_keluar_tersimpan_dengan_jurnal_seimbang(): void
    {
        $this->login();
        $rekening = $this->rekeningKas(1_000_000);
        $akunBeban = $this->akun('523');

        $response = $this->post(route('kas-keluar.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'keterangan' => 'Bayar listrik',
            'items' => [['akun_id' => $akunBeban->id, 'keterangan' => 'Listrik', 'nominal' => 150000]],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $kasKeluar = KasKeluar::firstOrFail();
        $this->assertMatchesRegularExpression('#^KK/\d{2}/\d{4}/\d{4}$#', $kasKeluar->nomor);

        $jurnal = $kasKeluar->jurnal;
        $this->assertNotNull($jurnal);
        $this->assertEquals(150000, (float) $jurnal->items->sum('debit'));
        $this->assertEquals(150000, (float) $jurnal->items->sum('kredit'));
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $akunBeban->id && (float) $item->debit === 150000.0));
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $rekening->akun_id && (float) $item->kredit === 150000.0));
    }

    public function test_kas_keluar_tetap_tersimpan_dengan_warning_saat_saldo_tidak_cukup(): void
    {
        $this->login();
        $rekening = $this->rekeningKas(100_000);
        $akunBeban = $this->akun('523');

        $response = $this->post(route('kas-keluar.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'keterangan' => 'Bayar listrik',
            'items' => [['akun_id' => $akunBeban->id, 'keterangan' => 'Listrik', 'nominal' => 150000]],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('warning');

        $this->assertDatabaseCount('kas_keluar', 1);
        $this->assertEquals(-50000, (float) $rekening->fresh()->saldo);
    }

    public function test_mutasi_bank_tersimpan_dengan_jurnal_seimbang(): void
    {
        $this->login();
        $asal = $this->rekeningBank(10_000_000);
        $tujuan = $this->rekeningKas();

        $response = $this->post(route('mutasi-bank.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_asal_id' => $asal->id,
            'rekening_tujuan_id' => $tujuan->id,
            'nominal' => 2500000,
            'keterangan' => 'Transfer ke kas',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $mutasi = MutasiBank::firstOrFail();
        $this->assertMatchesRegularExpression('#^MB/\d{2}/\d{4}/\d{4}$#', $mutasi->nomor);

        $jurnal = $mutasi->jurnal;
        $this->assertNotNull($jurnal);
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $tujuan->akun_id && (float) $item->debit === 2500000.0));
        $this->assertTrue($jurnal->items->contains(fn ($item) => $item->akun_id === $asal->akun_id && (float) $item->kredit === 2500000.0));
    }

    public function test_mutasi_bank_tetap_tersimpan_dengan_warning_saat_saldo_asal_tidak_cukup(): void
    {
        $this->login();
        $asal = $this->rekeningBank(100_000);
        $tujuan = $this->rekeningKas();

        $response = $this->post(route('mutasi-bank.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_asal_id' => $asal->id,
            'rekening_tujuan_id' => $tujuan->id,
            'nominal' => 500000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('warning');

        $this->assertDatabaseCount('mutasi_bank', 1);
        $this->assertEquals(-400000, (float) $asal->fresh()->saldo);
        $this->assertEquals(500000, (float) $tujuan->fresh()->saldo);
    }

    public function test_rekening_baru_ditolak_saat_akun_sudah_dipakai_rekening_lain(): void
    {
        $this->login();
        $this->rekeningKas(0);
        $akunKas = $this->akun('111');

        $response = $this->post(route('rekening.store'), [
            'jenis' => 'kas',
            'nama' => 'Kas Kedua',
            'akun_id' => $akunKas->id,
            'saldo_awal' => 0,
            'is_aktif' => true,
        ]);

        $response->assertSessionHasErrors('akun_id');
        $this->assertDatabaseCount('rekenings', 1);
    }

    public function test_akun_rekening_tidak_bisa_diubah_setelah_dipakai_transaksi(): void
    {
        $this->login();
        $rekening = $this->rekeningKas(1_000_000);
        $akunBeban = $this->akun('523');

        $this->post(route('kas-keluar.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'items' => [['akun_id' => $akunBeban->id, 'keterangan' => 'Sewa', 'nominal' => 100000]],
        ])->assertSessionHas('success');

        $akunBaru = AkunPerkiraan::create([
            'kode' => '1111',
            'nama' => 'Kas Cabang',
            'jenis' => 'aset',
            'saldo_normal' => 'debit',
            'is_header' => false,
            'parent_id' => $this->akun('11')->id,
        ]);

        $response = $this->patch(route('rekening.update', $rekening), [
            'jenis' => 'kas',
            'nama' => 'Kas Lokal',
            'akun_id' => $akunBaru->id,
            'saldo_awal' => 1000000,
            'is_aktif' => true,
        ]);

        $response->assertSessionHasErrors('akun_id');
        $this->assertSame($this->akun('111')->id, $rekening->fresh()->akun_id);
    }

    public function test_saldo_rekening_berbeda_akun_tidak_saling_tercampur(): void
    {
        $this->login();
        $akunKas = $this->akun('111');
        $akunKasBaru = AkunPerkiraan::create([
            'kode' => '1111',
            'nama' => 'Kas Cabang',
            'jenis' => 'aset',
            'saldo_normal' => 'debit',
            'is_header' => false,
            'parent_id' => $this->akun('11')->id,
        ]);

        $r1 = Rekening::create(['jenis' => 'kas', 'nama' => 'Kas A', 'akun_id' => $akunKas->id, 'saldo_awal' => 0, 'is_aktif' => true]);
        $r2 = Rekening::create(['jenis' => 'kas', 'nama' => 'Kas B', 'akun_id' => $akunKasBaru->id, 'saldo_awal' => 0, 'is_aktif' => true]);

        JournalService::post('manual', now()->toDateString(), [
            ['akun_id' => $akunKas->id, 'debit' => 100000, 'kredit' => 0],
            ['akun_id' => $this->akun('31')->id, 'debit' => 0, 'kredit' => 100000],
        ], 'Setoran tunai');

        $this->assertEquals(100000, (float) $r1->fresh()->saldo);
        $this->assertEquals(0, (float) $r2->fresh()->saldo);
    }

    public function test_saldo_rekening_menjumlahkan_saldo_awal_dan_transaksi(): void
    {
        $this->login();
        $akunKas = $this->akun('111');
        $akunBeban = $this->akun('523');

        $this->post(route('rekening.store'), [
            'jenis' => 'kas',
            'nama' => 'Kas Tunai',
            'akun_id' => $akunKas->id,
            'saldo_awal' => 500000,
            'is_aktif' => true,
        ])->assertSessionHas('success');

        $rekening = Rekening::where('nama', 'Kas Tunai')->firstOrFail();
        $this->assertEquals(500000, (float) $rekening->fresh()->saldo);

        $this->post(route('kas-keluar.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'keterangan' => 'Operasional',
            'items' => [['akun_id' => $akunBeban->id, 'keterangan' => 'ATK', 'nominal' => 200000]],
        ])->assertSessionHas('success');

        $this->assertEquals(300000, (float) $rekening->fresh()->saldo);
    }

    public function test_tutup_buku_menutup_periode_dan_membuat_jurnal_penutup(): void
    {
        $this->login();
        $periode = PeriodeAkuntansi::where('is_open', true)->firstOrFail();
        $hari = now()->toDateString();

        $akunPenjualan = $this->akun('411')->id;
        $akunBeban = $this->akun('521')->id;
        $akunBarang = $this->akun('115')->id;

        JournalService::post('manual', $hari, [
            ['akun_id' => $akunBarang, 'debit' => 500000, 'kredit' => 0],
            ['akun_id' => $akunPenjualan, 'debit' => 0, 'kredit' => 500000],
        ], 'Penjualan tes');

        JournalService::post('manual', $hari, [
            ['akun_id' => $akunBeban, 'debit' => 200000, 'kredit' => 0],
            ['akun_id' => $akunBarang, 'debit' => 0, 'kredit' => 200000],
        ], 'Beban gaji tes');

        $response = $this->post(route('tutup-buku.store'), ['keterangan' => 'Tutup buku bulanan']);

        $response->assertRedirect(route('tutup-buku.index'));
        $response->assertSessionHas('success');

        $this->assertTrue($periode->fresh()->is_closed);

        $tutupBuku = TutupBuku::firstOrFail();
        $this->assertEquals($periode->id, $tutupBuku->periode_id);
        $this->assertEquals(500000, (float) $tutupBuku->total_pendapatan);
        $this->assertEquals(200000, (float) $tutupBuku->total_beban);
        $this->assertEquals(300000, (float) $tutupBuku->laba_rugi);

        $jurnalPenutup = JurnalUmum::where('tipe', 'tutup_buku')->firstOrFail();
        $this->assertTrue($jurnalPenutup->items->contains(fn ($item) => $item->akun_id === $akunPenjualan && (float) $item->debit === 500000.0));
        $this->assertTrue($jurnalPenutup->items->contains(fn ($item) => $item->akun_id === $akunBeban && (float) $item->kredit === 200000.0));
        $this->assertTrue($jurnalPenutup->items->contains(fn ($item) => $item->akun_id === $this->akun('32')->id && (float) $item->kredit === 300000.0));
        $this->assertEquals($jurnalPenutup->items->sum('debit'), $jurnalPenutup->items->sum('kredit'));
    }

    public function test_kas_masuk_multi_kategori_pelunasan_tanpa_faktur_ditolak(): void
    {
        $this->login();
        $rekening = $this->rekeningKas();
        $customer = Customer::create(['kode' => 'C002', 'nama' => 'PT Campur', 'is_aktif' => true]);

        $response = $this->post(route('kas-masuk.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'customer_id' => $customer->id,
            'keterangan' => 'Campuran',
            'items' => [
                ['akun_id' => $this->akun('113')->id, 'keterangan' => 'Pelunasan', 'nominal' => 100000],
                ['akun_id' => $this->akun('421')->id, 'keterangan' => 'Lain-lain', 'nominal' => 50000],
            ],
        ]);

        // Bagian pelunasan piutang diblokir karena customer tidak punya piutang.
        $response->assertSessionHas('error');
        $this->assertSame(0, KasMasuk::count());
        $this->assertSame(0, BbPiutang::count());
    }

    public function test_kas_keluar_multi_kategori_pembayaran_tanpa_faktur_ditolak(): void
    {
        $this->login();
        $rekening = $this->rekeningKas(1_000_000);
        $supplier = Supplier::create(['kode' => 'S001', 'nama' => 'PT Pemasok', 'is_aktif' => true]);

        $response = $this->post(route('kas-keluar.store'), [
            'tanggal' => now()->toDateString(),
            'rekening_id' => $rekening->id,
            'supplier_id' => $supplier->id,
            'keterangan' => 'Campuran',
            'items' => [
                ['akun_id' => $this->akun('211')->id, 'keterangan' => 'Bayar utang', 'nominal' => 100000],
                ['akun_id' => $this->akun('523')->id, 'keterangan' => 'Operasional', 'nominal' => 50000],
            ],
        ]);

        // Bagian pembayaran hutang diblokir karena supplier tidak punya hutang.
        $response->assertSessionHas('error');
        $this->assertSame(0, KasKeluar::count());
        $this->assertSame(0, BbHutang::count());
    }

    public function test_tutup_buku_saldo_pendapatan_dan_beban_kontra_tetap_balance(): void
    {
        $this->login();
        $periode = PeriodeAkuntansi::where('is_open', true)->firstOrFail();
        $hari = now()->toDateString();

        // Pendapatan net negatif (retur > penjualan): Dr 411, Cr kas
        JournalService::post('manual', $hari, [
            ['akun_id' => $this->akun('411')->id, 'debit' => 50000, 'kredit' => 0],
            ['akun_id' => $this->akun('111')->id, 'debit' => 0, 'kredit' => 50000],
        ], 'Retur penjualan');
        // Beban net negatif (koreksi balik beban): Cr 521, Dr kas
        JournalService::post('manual', $hari, [
            ['akun_id' => $this->akun('111')->id, 'debit' => 5000, 'kredit' => 0],
            ['akun_id' => $this->akun('521')->id, 'debit' => 0, 'kredit' => 5000],
        ], 'Koreksi beban');

        $response = $this->post(route('tutup-buku.store'), ['keterangan' => 'Tutup kontra']);
        $response->assertSessionHas('success');

        $tutupBuku = TutupBuku::firstOrFail();
        $this->assertEquals(-45000, (float) $tutupBuku->laba_rugi);

        $jurnalPenutup = JurnalUmum::where('tipe', 'tutup_buku')->firstOrFail();
        $this->assertTrue($jurnalPenutup->items->contains(fn ($item) => $item->akun_id === $this->akun('411')->id && (float) $item->kredit === 50000.0));
        $this->assertTrue($jurnalPenutup->items->contains(fn ($item) => $item->akun_id === $this->akun('521')->id && (float) $item->debit === 5000.0));
        $this->assertTrue($jurnalPenutup->items->contains(fn ($item) => $item->akun_id === $this->akun('32')->id && (float) $item->debit === 45000.0));
        $this->assertEqualsWithDelta($jurnalPenutup->items->sum('debit'), $jurnalPenutup->items->sum('kredit'), 0.01);
    }
}
