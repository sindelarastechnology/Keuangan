<?php

namespace Tests\Feature;

use App\Models\AkunPerkiraan;
use App\Models\JurnalUmum;
use App\Models\PeriodeAkuntansi;
use App\Models\User;
use App\Services\JournalService;
use App\Services\PeriodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ApprovalWorkflowTest extends TestCase
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

    private function postJurnal(?string $tanggal = null): JurnalUmum
    {
        $akun = AkunPerkiraan::where('kode', '115')->firstOrFail()->id;

        return JournalService::post('manual', $tanggal ?? now()->toDateString(), [
            ['akun_id' => $akun, 'debit' => 100000, 'kredit' => 0],
            ['akun_id' => $akun, 'debit' => 0, 'kredit' => 100000],
        ], 'Jurnal tes persetujuan');
    }

    public function test_jurnal_auto_posted_berstatus_approved_dan_mencatat_pembuat(): void
    {
        $user = $this->login();
        $jurnal = $this->postJurnal();

        $this->assertEquals('approved', $jurnal->approval_status);
        $this->assertEquals($user->id, $jurnal->created_by);
        $this->assertEquals($user->id, $jurnal->approved_by);
        $this->assertTrue($jurnal->isApproved());
    }

    public function test_alur_request_approve_dan_reject_membuat_audit_trail(): void
    {
        $user = $this->login();
        $jurnal = $this->postJurnal();

        $jurnal->requestApproval();
        $this->assertTrue($jurnal->fresh()->isPendingApproval());

        $jurnal->approve($user);
        $this->assertTrue($jurnal->fresh()->isApproved());
        $this->assertEquals($user->id, $jurnal->fresh()->approved_by);

        $jurnal->fresh()->requestApproval();
        $jurnal->fresh()->reject($user, 'Nominal tidak sesuai');
        $this->assertTrue($jurnal->fresh()->isRejected());
        $this->assertEquals('Nominal tidak sesuai', $jurnal->fresh()->approval_reason);

        $this->assertEquals(4, $jurnal->fresh()->auditTrails()->count());
        $this->assertTrue($jurnal->fresh()->auditTrails()->where('action', 'approved')->exists());
        $this->assertTrue($jurnal->fresh()->auditTrails()->where('action', 'rejected')->exists());
    }

    public function test_periode_dikunci_menolak_jurnal_baru(): void
    {
        $this->login();
        $akun = AkunPerkiraan::where('kode', '115')->firstOrFail()->id;
        $bulan = now()->format('m');
        $tahun = (string) now()->year;
        $periode = PeriodeAkuntansi::firstOrCreate(
            ['bulan' => $bulan, 'tahun' => $tahun],
            ['kode' => $tahun.$bulan]
        );

        PeriodeService::kunci($periode, 'Tutup buku');
        $this->assertTrue($periode->refresh()->is_locked);

        $this->expectException(RuntimeException::class);
        JournalService::post('manual', now()->toDateString(), [
            ['akun_id' => $akun, 'debit' => 50000, 'kredit' => 0],
            ['akun_id' => $akun, 'debit' => 0, 'kredit' => 50000],
        ], 'Seharusnya ditolak oleh locking periode');
    }

    public function test_route_approve_jurnal_via_http(): void
    {
        $this->login();
        $jurnal = $this->postJurnal();
        $jurnal->requestApproval();

        $this->post(route('jurnal.approve', $jurnal))->assertSessionHasNoErrors();
        $this->assertEquals('approved', $jurnal->fresh()->approval_status);
    }

    public function test_semua_pengguna_authenticated_dapat_menyetujui_jurnal(): void
    {
        $user = $this->login();
        $jurnal = $this->postJurnal();
        $jurnal->requestApproval();

        $this->assertTrue($jurnal->canApprove($user));
        $this->post(route('jurnal.approve', $jurnal))->assertSessionHasNoErrors();
        $this->assertEquals('approved', $jurnal->fresh()->approval_status);
    }

    public function test_route_approve_mengharuskan_login(): void
    {
        $this->login();
        $jurnal = $this->postJurnal();
        $jurnal->requestApproval();

        auth()->logout();

        $this->post(route('jurnal.approve', $jurnal))->assertRedirect(route('login'));
        $this->assertEquals('pending_review', $jurnal->fresh()->approval_status);
    }

    public function test_route_reject_jurnal_membutuhkan_alasan(): void
    {
        $this->login();
        $jurnal = $this->postJurnal();
        $jurnal->requestApproval();

        $this->post(route('jurnal.reject', $jurnal))->assertSessionHasErrors('reason');
        $this->assertEquals('pending_review', $jurnal->fresh()->approval_status);
    }
}
