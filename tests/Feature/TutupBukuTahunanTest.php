<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\JurnalUmum;
use App\Models\PeriodeAkuntansi;
use App\Models\TutupBukuTahunan;
use App\Models\User;
use App\Services\JournalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutupBukuTahunanTest extends TestCase
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

    private function akun(string $kode): AkunPerkiraan
    {
        return AkunPerkiraan::where('kode', $kode)->firstOrFail();
    }

    private function tahunBerjalan(): int
    {
        return (int) now()->format('Y');
    }

    public function test_index_menampilkan_tahun_dan_status(): void
    {
        $this->login();

        $this->get(route('tutup-buku-tahunan.index'))
            ->assertOk()
            ->assertSee((string) $this->tahunBerjalan())
            ->assertSee('12/12')
            ->assertSee('Terbuka')
            ->assertSee('Tutup Buku Tahunan');
    }

    public function test_store_menutup_semua_periode_dan_membuat_jurnal_penutup(): void
    {
        $this->login();
        $tahun = $this->tahunBerjalan();
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

        $response = $this->post(route('tutup-buku-tahunan.store'), [
            'tahun' => (string) $tahun,
            'keterangan' => 'Tutup tahunan tes',
        ]);

        $response->assertRedirect(route('tutup-buku-tahunan.index'));
        $response->assertSessionHas('success');

        $tutup = TutupBukuTahunan::firstOrFail();
        $this->assertEquals($tahun, (int) $tutup->tahun);
        $this->assertEquals(500000, (float) $tutup->total_pendapatan);
        $this->assertEquals(200000, (float) $tutup->total_beban);
        $this->assertEquals(300000, (float) $tutup->laba_rugi);

        $periode = PeriodeAkuntansi::where('tahun', (string) $tahun)->get();
        $this->assertCount(12, $periode);
        $this->assertTrue($periode->every(fn ($p) => $p->is_closed && ! $p->is_open));

        $jurnalPenutup = JurnalUmum::where('tipe', 'tutup_buku')
            ->where('tanggal', $tahun.'-12-31')
            ->firstOrFail();
        $this->assertTrue($jurnalPenutup->items->contains(fn ($item) => $item->akun_id === $akunPenjualan && (float) $item->debit === 500000.0));
        $this->assertTrue($jurnalPenutup->items->contains(fn ($item) => $item->akun_id === $akunBeban && (float) $item->kredit === 200000.0));
        $this->assertTrue($jurnalPenutup->items->contains(fn ($item) => $item->akun_id === $this->akun('32')->id && (float) $item->kredit === 300000.0));
        $this->assertEquals($jurnalPenutup->items->sum('debit'), $jurnalPenutup->items->sum('kredit'));
    }

    public function test_store_ulang_tahun_yang_sama_ditolak(): void
    {
        $this->login();
        $tahun = $this->tahunBerjalan();

        $this->post(route('tutup-buku-tahunan.store'), ['tahun' => (string) $tahun])
            ->assertRedirect(route('tutup-buku-tahunan.index'))
            ->assertSessionHas('success');

        $this->post(route('tutup-buku-tahunan.store'), ['tahun' => (string) $tahun])
            ->assertSessionHas('error');

        $this->assertSame(1, TutupBukuTahunan::count());
    }

    public function test_store_ditolak_saat_ada_periode_bulan_ditutup(): void
    {
        $this->login();
        $tahun = $this->tahunBerjalan();

        PeriodeAkuntansi::where('tahun', (string) $tahun)->first()->update(['is_closed' => true]);

        $this->post(route('tutup-buku-tahunan.store'), ['tahun' => (string) $tahun])
            ->assertSessionHas('error');

        $this->assertSame(0, TutupBukuTahunan::count());
        $this->assertSame(0, JurnalUmum::count());
    }

    public function test_store_tahun_tanpa_jurnal_tetap_mencatat_dan_menutup(): void
    {
        $this->login();
        $tahun = $this->tahunBerjalan();

        $this->post(route('tutup-buku-tahunan.store'), ['tahun' => (string) $tahun])
            ->assertSessionHas('success');

        $this->assertSame(1, TutupBukuTahunan::count());
        $this->assertTrue(PeriodeAkuntansi::where('tahun', (string) $tahun)->where('is_closed', false)->doesntExist());
        $this->assertSame(0, JurnalUmum::where('tipe', 'tutup_buku')->count());
    }
}
