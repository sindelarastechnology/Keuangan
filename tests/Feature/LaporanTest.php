<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\User;
use App\Services\JournalService;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function login(): void
    {
        $this->actingAs(User::where('email', 'admin@keuangan.test')->firstOrFail());
    }

    private function akun(string $kode): int
    {
        return AkunPerkiraan::where('kode', $kode)->firstOrFail()->id;
    }

    private function postingPenjualanDanBeban(): void
    {
        $hari = now()->toDateString();

        // DR Kas 111, CR Penjualan 411 = 750000 (pendapatan)
        JournalService::post('kas_masuk', $hari, [
            ['akun_id' => $this->akun('111'), 'debit' => 750000, 'kredit' => 0],
            ['akun_id' => $this->akun('411'), 'debit' => 0, 'kredit' => 750000],
        ], 'Penjualan tunai');

        // DR Beban sewa 521, CR Kas 111 = 200000 (beban)
        JournalService::post('kas_keluar', $hari, [
            ['akun_id' => $this->akun('521'), 'debit' => 200000, 'kredit' => 0],
            ['akun_id' => $this->akun('111'), 'debit' => 0, 'kredit' => 200000],
        ], 'Bayar sewa');
    }

    public function test_laba_rugi_menghitung_total_pendapatan_dan_beban(): void
    {
        $this->login();
        $this->postingPenjualanDanBeban();

        $data = ReportService::labaRugi(now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString());

        $this->assertEquals(750000, round($data['total_pendapatan'], 2));
        $this->assertEquals(200000, round($data['total_beban'], 2));
        $this->assertEquals(550000, round($data['laba_rugi'], 2));
    }

    public function test_neraca_seimbang_aset_sama_dengan_kewajiban_plus_modal(): void
    {
        $this->login();
        $this->postingPenjualanDanBeban();

        $data = ReportService::neraca(now()->toDateString());

        $this->assertTrue($data['is_balanced']);

        // Kas 111 = 750000 - 200000 = 550000
        $kas = collect($data['aset'])->firstWhere('akun.kode', '111');
        $this->assertNotNull($kas);
        $this->assertEquals(550000, round($kas['saldo'], 2));

        // Modal: laba berjalan tidak boleh 0
        $this->assertNotEquals(0.0, round($data['laba_rugi_berjalan'], 2));
        $this->assertEquals(550000, round($data['laba_rugi_berjalan'], 2));
    }

    public function test_halaman_laba_rugi_menampilkan_angka_yang_benar(): void
    {
        $this->login();
        $this->postingPenjualanDanBeban();

        $response = $this->get(route('laporan.laba-rugi', [
            'dari' => now()->startOfMonth()->toDateString(),
            'sampai' => now()->endOfMonth()->toDateString(),
        ]));

        $response->assertOk()
            ->assertSee('Rp 750.000')
            ->assertSee('Rp 200.000')
            ->assertSee('Rp 550.000');
    }

    public function test_halaman_neraca_menampilkan_total_aset_dan_seimbang(): void
    {
        $this->login();
        $this->postingPenjualanDanBeban();

        $response = $this->get(route('laporan.neraca', ['sampai' => now()->toDateString()]));

        $response->assertOk()
            ->assertSee('Rp 550.000')
            ->assertSee('Neraca Seimbang (Balanced)');
    }

    public function test_halaman_laporan_kosong_tetap_merender(): void
    {
        $this->login();

        $this->get(route('laporan.laba-rugi'))
            ->assertOk()
            ->assertSee('Belum ada pendapatan');

        $this->get(route('laporan.neraca'))
            ->assertOk()
            ->assertSee('Tidak ada aset');
    }

    public function test_arus_kas_memasukkan_setoran_modal_ke_aktivitas_pendanaan(): void
    {
        $this->login();

        // DR Kas 111, CR Modal 31 = 20000000 (setoran modal tunai)
        JournalService::post('manual', now()->toDateString(), [
            ['akun_id' => $this->akun('111'), 'debit' => 20000000, 'kredit' => 0],
            ['akun_id' => $this->akun('31'), 'debit' => 0, 'kredit' => 20000000],
        ], 'Setoran modal');

        $data = ReportService::arusKas(now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString());

        $this->assertEquals(20000000, round($data['kas_pendanaan'], 2));
        $this->assertEquals(20000000, round($data['kas_akhir'], 2));
        $this->assertEquals(0, round($data['selisih'], 2));
    }

    public function test_arus_kas_memasukkan_pembelian_aset_tetap_ke_aktivitas_investasi(): void
    {
        $this->login();

        // DR Peralatan 121, CR Kas 111 = 5000000 (pembelian aset tetap tunai)
        JournalService::post('manual', now()->toDateString(), [
            ['akun_id' => $this->akun('121'), 'debit' => 5000000, 'kredit' => 0],
            ['akun_id' => $this->akun('111'), 'debit' => 0, 'kredit' => 5000000],
        ], 'Beli peralatan');

        $data = ReportService::arusKas(now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString());

        $this->assertEquals(-5000000, round($data['kas_investasi'], 2));
        $this->assertEquals(0, round($data['selisih'], 2));
    }

    public function test_neraca_memperlakukan_akumulasi_penyusutan_sebagai_contra_aset(): void
    {
        $this->login();

        // Dr Beban Penyusutan (526), Cr Akumulasi Penyusutan (122) = 100000
        JournalService::post('manual', now()->toDateString(), [
            ['akun_id' => $this->akun('526'), 'debit' => 100000, 'kredit' => 0],
            ['akun_id' => $this->akun('122'), 'debit' => 0, 'kredit' => 100000],
        ], 'Penyusutan tes');

        $data = ReportService::neraca(now()->toDateString());

        $this->assertTrue($data['is_balanced']);

        $akumulasi = collect($data['aset'])->firstWhere('akun.kode', '122');
        $this->assertNotNull($akumulasi);
        $this->assertEquals(-100000, round($akumulasi['saldo'], 2));

        // Rugi (beban tanpa pendapatan) mengurangi modal; total aset = kontra akumulasi
        $this->assertEquals(-100000, round($data['total_aset'], 2));
        $labaBerjalan = collect($data['modal'])->firstWhere('is_laba_berjalan', true);
        $this->assertNotNull($labaBerjalan);
        $this->assertEquals(-100000, round($labaBerjalan['saldo'], 2));
    }

    public function test_halaman_neraca_menampilkan_laba_berjalan_tanpa_duplikat(): void
    {
        $this->login();
        $this->postingPenjualanDanBeban();

        $response = $this->get(route('laporan.neraca', ['sampai' => now()->toDateString()]));

        $response->assertOk();
        $this->assertSame(1, substr_count($response->getContent(), 'Laba / Rugi Berjalan'));
    }
}
