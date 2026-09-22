<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\Rekening;
use App\Models\User;
use App\Services\JournalService;
use App\Services\PengaturanSistemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RekeningIsolasiAkunTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_perintah_memindahkan_rekening_yang_berbagi_akun_ke_akun_masing_masing(): void
    {
        $user = User::where('email', 'admin@keuangan.test')->firstOrFail();
        $this->actingAs($user);

        $akunBank = AkunPerkiraan::where('kode', '112')->firstOrFail();

        // Simulasi data lama: tiga rekening memakai akun bank yang sama persis.
        $bank1 = Rekening::create(['jenis' => 'bank', 'nama' => 'Bank Satu', 'akun_id' => $akunBank->id, 'saldo_awal' => 0, 'is_aktif' => true]);
        $bank2 = Rekening::create(['jenis' => 'bank', 'nama' => 'Bank Dua', 'akun_id' => $akunBank->id, 'saldo_awal' => 0, 'is_aktif' => true]);
        $bank3 = Rekening::create(['jenis' => 'bank', 'nama' => 'Bank Tiga', 'akun_id' => $akunBank->id, 'saldo_awal' => 100000, 'is_aktif' => true]);

        // Bank Tiga punya jurnal pembuka (seperti yang dibuat RekeningController saat saldo_awal > 0).
        $akunModal = PengaturanSistemService::akunId('modal');
        JournalService::post('manual', now()->toDateString(), [
            ['akun_id' => $akunBank->id, 'debit' => 100000, 'kredit' => 0, 'keterangan' => 'Saldo Awal '.$bank3->nama],
            ['akun_id' => $akunModal, 'debit' => 0, 'kredit' => 100000, 'keterangan' => 'Setoran Modal Awal Rekening '.$bank3->nama],
        ], 'Saldo Awal Rekening '.$bank3->nama, $bank3);

        Artisan::call('rekening:isolasi-akun');

        $akunIds = Rekening::pluck('akun_id')->unique();
        $this->assertSame(3, $akunIds->count());

        // Rekening paling awal tetap memakai akun bersama; yang lain dapat akun baru.
        $this->assertSame($akunBank->id, $bank1->fresh()->akun_id);
        $this->assertNotSame($akunBank->id, $bank2->fresh()->akun_id);
        $this->assertNotSame($akunBank->id, $bank3->fresh()->akun_id);

        // Saldo tidak lagi tercampur: 0, 0, 100000.
        $this->assertEquals(0, (float) $bank1->fresh()->saldo);
        $this->assertEquals(0, (float) $bank2->fresh()->saldo);
        $this->assertEquals(100000, (float) $bank3->fresh()->saldo);

        // Akun baru merupakan turunan akun bank (kode berawalan 112).
        $this->assertStringStartsWith('112', AkunPerkiraan::find($bank3->fresh()->akun_id)->kode);
    }

    public function test_perintah_tidak_merubah_apa_apa_bila_tiap_rekening_sudah_memakai_akun_sendiri(): void
    {
        $user = User::where('email', 'admin@keuangan.test')->firstOrFail();
        $this->actingAs($user);

        $akunKas = AkunPerkiraan::where('kode', '111')->firstOrFail();
        $rekening = Rekening::create(['jenis' => 'kas', 'nama' => 'Kas Lokal', 'akun_id' => $akunKas->id, 'saldo_awal' => 0, 'is_aktif' => true]);

        Artisan::call('rekening:isolasi-akun');

        $this->assertSame($akunKas->id, $rekening->fresh()->akun_id);
        $this->assertSame(1, Rekening::count());
    }
}
