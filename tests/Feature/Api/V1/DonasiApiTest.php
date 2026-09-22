<?php

namespace Tests\Feature\Api\V1;

use App\Models\Donasi;
use App\Models\User;
use App\Notifications\NotifikasiDalamAplikasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DonasiApiTest extends TestCase
{
    use RefreshDatabase;

    private string $key = 'kunci-integrasi-admin-123';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        // DatabaseSeeder meng-Auth::login(admin). Keluarkan agar global scope
        // BelongsToUser tidak membatasi donasi milik user lain (API tanpa user web).
        auth()->logout();
        session()->invalidate();
        config()->set('integration.user_management.key', $this->key);
    }

    private function headers(): array
    {
        return ['X-Integration-Key' => $this->key];
    }

    public function test_api_menolak_tanpa_kunci(): void
    {
        $this->getJson('/api/v1/donasi')->assertStatus(401);
    }

    public function test_daftar_donasi_melihat_semua_users(): void
    {
        $userA = User::factory()->create(['name' => 'Penyumbang A']);
        $userB = User::factory()->create(['name' => 'Penyumbang B']);

        Donasi::factory()->for($userA)->create(['nominal' => 100000]);
        Donasi::factory()->for($userB)->create(['nominal' => 50000]);

        $this->getJson('/api/v1/donasi', $this->headers())
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2);
    }

    public function test_filter_status_dan_pencarian_pengguna(): void
    {
        $user = User::factory()->create(['name' => 'Zara Unik']);

        Donasi::factory()->for($user)->confirmed()->create();
        Donasi::factory()->for($user)->create();

        $this->getJson('/api/v1/donasi?status=confirmed', $this->headers())
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'confirmed');

        $this->getJson('/api/v1/donasi?q=zara', $this->headers())
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_detail_donasi_menyertakan_user_dan_bukti_url_null(): void
    {
        $user = User::factory()->create(['name' => 'Hamba', 'email' => 'hamba@contoh.id']);
        $donasi = Donasi::factory()->for($user)->create(['bukti_path' => null]);

        $this->getJson("/api/v1/donasi/{$donasi->id}", $this->headers())
            ->assertOk()
            ->assertJsonPath('data.id', $donasi->id)
            ->assertJsonPath('data.user.name', 'Hamba')
            ->assertJsonPath('data.user.email', 'hamba@contoh.id')
            ->assertJsonPath('data.bukti_url', null);
    }

    public function test_konfirmasi_donasi_mengirim_notifikasi(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $donasi = Donasi::factory()->for($user)->create();

        $this->patchJson("/api/v1/donasi/{$donasi->id}/status", [
            'status' => 'confirmed',
        ], $this->headers())
            ->assertOk()
            ->assertJsonPath('data.status', 'confirmed');

        $this->assertSame('confirmed', $donasi->refresh()->status);

        Notification::assertSentTo($user, NotifikasiDalamAplikasi::class);
    }

    public function test_status_tidak_valid_ditolak_422(): void
    {
        $donasi = Donasi::factory()->create();

        $this->patchJson("/api/v1/donasi/{$donasi->id}/status", [
            'status' => 'batal',
        ], $this->headers())
            ->assertStatus(422)
            ->assertJsonValidationErrors('status');

        $this->assertSame('pending', $donasi->refresh()->status);
    }
}
