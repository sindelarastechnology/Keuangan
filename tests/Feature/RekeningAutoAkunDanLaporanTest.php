<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\JurnalUmum;
use App\Models\Rekening;
use App\Models\User;
use App\Services\JournalService;
use App\Services\PengaturanSistemService;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekeningAutoAkunDanLaporanTest extends TestCase
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

    public function test_buat_rekening_tanpa_akun_menyiapkan_akun_kas_bank_baru(): void
    {
        $this->login();

        $this->post(route('rekening.store'), [
            'jenis' => 'bank',
            'nama' => 'Bank Satu',
            'saldo_awal' => 0,
            'is_aktif' => true,
        ])->assertSessionHas('success');

        $this->post(route('rekening.store'), [
            'jenis' => 'bank',
            'nama' => 'Bank Dua',
            'saldo_awal' => 0,
            'is_aktif' => true,
        ])->assertSessionHas('success');

        $r1 = Rekening::where('nama', 'Bank Satu')->firstOrFail();
        $r2 = Rekening::where('nama', 'Bank Dua')->firstOrFail();

        // Setiap rekening memakai akun turunan berbeda.
        $this->assertNotSame($r1->akun_id, $r2->akun_id);
        $this->assertSame(['1121', '1122'], [$r1->akun->kode, $r2->akun->kode]);

        foreach ([$r1, $r2] as $r) {
            $this->assertSame('aset', $r->akun->jenis);
            $this->assertSame('debit', $r->akun->saldo_normal);
            $this->assertFalse($r->akun->is_header);
        }
    }

    public function test_buat_rekening_tanpa_akun_dengan_saldo_awal_mencatat_jurnal_pembuka(): void
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
        $this->assertStringStartsWith('112', $rekening->akun->kode);
        $this->assertEquals(250000, (float) $rekening->fresh()->saldo);

        // Jurnal pembuka dibuat di atas akun otomatis tersebut.
        $jurnal = JurnalUmum::where('ref_type', Rekening::class)
            ->where('ref_id', $rekening->id)
            ->where('keterangan', 'like', 'Saldo Awal%')
            ->firstOrFail();

        $this->assertTrue(
            $jurnal->items->contains(fn ($item) => $item->akun_id === $rekening->akun_id && (float) $item->debit == 250000)
        );
    }

    public function test_update_tanpa_akun_mempertahankan_akun_sekarang(): void
    {
        $this->login();
        $rekening = Rekening::create(['jenis' => 'kas', 'nama' => 'Kas Induk', 'akun_id' => $this->akun('111'), 'saldo_awal' => 0, 'is_aktif' => true]);

        $this->patch(route('rekening.update', $rekening), [
            'jenis' => 'kas',
            'nama' => 'Kas Induk Baru',
            'saldo_awal' => 0,
            'is_aktif' => true,
        ])->assertSessionHas('success');

        $this->assertSame($this->akun('111'), $rekening->fresh()->akun_id);
    }

    public function test_arus_kas_memperhitungkan_akun_kas_bank_turunan(): void
    {
        $this->login();

        $akunAnak = AkunPerkiraan::create([
            'kode' => '1121',
            'nama' => 'Bank Anak',
            'jenis' => 'aset',
            'saldo_normal' => 'debit',
            'is_header' => false,
            'parent_id' => $this->akun('11'),
        ]);

        JournalService::post('kas_masuk', now()->toDateString(), [
            ['akun_id' => $akunAnak->id, 'debit' => 750000, 'kredit' => 0],
            ['akun_id' => $this->akun('411'), 'debit' => 0, 'kredit' => 750000],
        ], 'Penjualan via bank anak');

        $akunSet = PengaturanSistemService::akunKasBank();
        $this->assertTrue($akunSet->contains('id', $akunAnak->id));

        $data = ReportService::arusKas(now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString());

        // Tanpa perbaikan, kas_akhir mengecil ke Rp 0 karena akun anak tidak
        // dihitung sebagai kas; kini selisih harus nol.
        $this->assertEquals(750000, round($data['kas_akhir'], 2));
        $this->assertEquals(0, round($data['selisih'], 2));
    }

    public function test_void_jurnal_menyembunyikan_pasangan_dari_laporan_dan_buku_besar(): void
    {
        $this->login();
        $akunKas = $this->akun('111');

        JournalService::post('manual', now()->toDateString(), [
            ['akun_id' => $akunKas, 'debit' => 500000, 'kredit' => 0],
            ['akun_id' => $this->akun('31'), 'debit' => 0, 'kredit' => 500000],
        ], 'Setoran awal');

        $jurnal = JurnalUmum::where('keterangan', 'Setoran awal')->firstOrFail();

        // Sebelum void, jurnal tampil di buku besar.
        $this->get(route('buku-besar.index', ['akun_id' => $akunKas]))
            ->assertOk()
            ->assertSee('Setoran awal');

        JournalService::void($jurnal, 'Pembatalan tes');

        // Original ditandai void; jurnal balik mereferensikan jurnal asli.
        $this->assertNotNull($jurnal->fresh()->voided_at);
        $this->assertSame($jurnal->id, JurnalUmum::where('ref_type', JurnalUmum::class)->where('ref_id', $jurnal->id)->value('ref_id'));

        // Pasangan void + balik dikeluarkan dari scope laporan.
        $this->assertSame(0, JurnalUmum::tanpaVoid()->count());

        // Detail buku besar tidak lagi memperlihatkan baris ganda.
        $this->get(route('buku-besar.index', ['akun_id' => $akunKas]))
            ->assertOk()
            ->assertDontSee('[DIBATALKAN]')
            ->assertDontSee('Setoran awal')
            ->assertSee('Tidak ada mutasi.');

        // Agregat kembali nol dan neraca tetap seimbang.
        $this->assertEquals(0, round(ReportService::saldoAkunKumulatif($akunKas, now()->toDateString()), 2));
        $this->assertEquals(0, round(ReportService::saldoAkunRentang($akunKas, now()->startOfMonth()->toDateString(), now()->toDateString()), 2));
        $this->assertTrue(ReportService::neraca(now()->toDateString())['is_balanced']);
    }
}
