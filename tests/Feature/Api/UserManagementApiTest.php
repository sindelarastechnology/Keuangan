<?php

namespace Tests\Feature\Api;

use App\Models\AkunPerkiraan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementApiTest extends TestCase
{
    use RefreshDatabase;

    private string $key = 'test-integration-key';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        config()->set('integration.user_management.key', $this->key);
    }

    private function headers(?string $key = null): array
    {
        return ['X-Integration-Key' => $key ?? $this->key];
    }

    private function logoutSeededAdmin(): void
    {
        // DatabaseSeeder meng-Auth::login(admin); kosongkan agar alur guest bisa diuji.
        auth()->logout();
        session()->invalidate();
    }

    public function test_request_tanpa_key_ditolak_401(): void
    {
        $this->getJson('/api/v1/users')
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized.']);
    }

    public function test_request_dengan_key_salah_ditolak_401(): void
    {
        $this->getJson('/api/v1/users', $this->headers('salah'))
            ->assertStatus(401);
    }

    public function test_daftar_user_memakai_pagination_dan_pencarian(): void
    {
        User::factory()->count(25)->create(['name' => fn () => 'Biasa']);

        $unik = User::factory()->create(['name' => 'Zara Unik', 'email' => 'zara.unik@contoh.id']);

        $this->getJson('/api/v1/users?per_page=10', $this->headers())
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.total', 27)
            ->assertJsonPath('meta.per_page', 10);

        $this->getJson('/api/v1/users?q=zara', $this->headers())
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $unik->id);
    }

    public function test_buat_user_baru_memverifikasi_dan_menyiapkan_tenant(): void
    {
        $rawPassword = 'rahasia-kuat-123';

        $response = $this->postJson('/api/v1/users', [
            'name' => 'Akun Baru',
            'email' => 'akun.baru@contoh.id',
            'password' => $rawPassword,
            'password_confirmation' => $rawPassword,
        ], $this->headers())
            ->assertCreated()
            ->assertJsonPath('data.email', 'akun.baru@contoh.id')
            ->assertJsonPath('data.status', 'aktif')
            ->assertJsonPath('data.name', 'Akun Baru')
            ->assertJsonStructure(['data' => ['id', 'name', 'email', 'email_verified_at', 'status']]);

        $user = User::where('email', 'akun.baru@contoh.id')->firstOrFail();

        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check($rawPassword, $user->password));

        // Tenant sudah siap: chart of account & akun kunci inti (526 termasuk).
        $akunUser = AkunPerkiraan::withoutGlobalScope('belongsToUser')->where('user_id', $user->id);
        $this->assertGreaterThan(0, $akunUser->count());
        $this->assertTrue($akunUser->clone()->where('kode', '111')->exists());
        $this->assertTrue($akunUser->clone()->where('kode', '526')->exists());
    }

    public function test_email_duplikat_ditolak_422(): void
    {
        $payload = [
            'name' => 'Duplikat',
            'email' => 'duplikat@contoh.id',
            'password' => 'rahasia-kuat-123',
            'password_confirmation' => 'rahasia-kuat-123',
        ];

        $this->postJson('/api/v1/users', $payload, $this->headers())
            ->assertCreated();

        $this->postJson('/api/v1/users', $payload, $this->headers())
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_detail_user(): void
    {
        $user = User::factory()->create();

        $this->getJson("/api/v1/users/{$user->id}", $this->headers())
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.status', 'aktif')
            ->assertJsonMissing(['password'])
            ->assertJsonMissing(['google_id']);
    }

    public function test_update_user_mengganti_nama_email_dan_email_sendiri_tetap_valid(): void
    {
        $user = User::factory()->create();

        $this->putJson("/api/v1/users/{$user->id}", [
            'name' => 'Nama Baru',
            'email' => $user->email,
        ], $this->headers())
            ->assertOk()
            ->assertJsonPath('data.name', 'Nama Baru');

        $user->refresh();
        $this->assertSame('Nama Baru', $user->name);

        $this->putJson("/api/v1/users/{$user->id}", [
            'name' => 'Nama Baru',
            'email' => 'ganti.juga@contoh.id',
        ], $this->headers())
            ->assertOk();

        $user->refresh();
        $this->assertSame('ganti.juga@contoh.id', $user->email);
    }

    public function test_update_password_hanya_diganti_bila_diisi(): void
    {
        $user = User::factory()->create();

        // Tidak mengirim password: tetap tidak berubah.
        $this->putJson("/api/v1/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
        ], $this->headers())->assertOk();

        $passwordLama = $user->refresh()->password;

        $baru = 'password-anyar-456';
        $this->putJson("/api/v1/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $baru,
            'password_confirmation' => $baru,
        ], $this->headers())->assertOk();

        $user->refresh();
        $this->assertNotSame($passwordLama, $user->password);
        $this->assertTrue(Hash::check($baru, $user->password));
    }

    public function test_status_tidak_valid_ditolak_422(): void
    {
        $user = User::factory()->create();

        $this->patchJson("/api/v1/users/{$user->id}/status", [
            'status' => 'banned',
        ], $this->headers())
            ->assertStatus(422)
            ->assertJsonValidationErrors('status');
    }

    public function test_suspend_memblokir_login_dan_akses_web(): void
    {
        $user = User::factory()->create();

        $this->patchJson("/api/v1/users/{$user->id}/status", [
            'status' => 'suspended',
        ], $this->headers())
            ->assertOk()
            ->assertJsonPath('data.status', 'suspended');

        $user->refresh();
        $this->assertSame('suspended', $user->status);
        $this->assertFalse($user->isAktif());

        // Login lewat form ditolak.
        $this->logoutSeededAdmin();
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        // Akses halaman web yang sudah login diblokir.
        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_aktivasi_kembali_setelah_suspend_memulihkan_akses(): void
    {
        $user = User::factory()->create();

        $this->patchJson("/api/v1/users/{$user->id}/status", ['status' => 'suspended'], $this->headers())
            ->assertOk();

        $this->patchJson("/api/v1/users/{$user->id}/status", ['status' => 'aktif'], $this->headers())
            ->assertOk()
            ->assertJsonPath('data.status', 'aktif');

        $this->logoutSeededAdmin();
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_hapus_user(): void
    {
        $target = User::factory()->create();
        User::factory()->create();

        $this->deleteJson("/api/v1/users/{$target->id}", [], $this->headers())
            ->assertOk()
            ->assertJson(['message' => 'Akun dihapus.']);

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_user_terakhir_tidak_bisa_dihapus(): void
    {
        User::query()->delete();
        $satu = User::factory()->create();

        $this->deleteJson("/api/v1/users/{$satu->id}", [], $this->headers())
            ->assertStatus(422)
            ->assertJsonValidationErrors('user');

        $this->assertDatabaseHas('users', ['id' => $satu->id]);
    }
}
