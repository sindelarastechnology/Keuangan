<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DefaultPlanProTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_registrasi_web_membuat_akun_pro(): void
    {
        // DatabaseSeeder meng-Auth::login(admin); keluar agar rute guest bisa diakses.
        auth()->logout();
        session()->invalidate();

        $payload = [
            'name' => 'User Baru',
            'email' => 'user.baru@contoh.id',
            'password' => 'Password-kuat-123',
            'password_confirmation' => 'Password-kuat-123',
        ];

        $this->post(route('register'), $payload);

        $user = User::where('email', 'user.baru@contoh.id')->firstOrFail();

        $this->assertSame('pro', $user->plan);
    }

    public function test_registrasi_api_membuat_akun_pro(): void
    {
        config()->set('integration.user_management.key', 'kunci-integrasi-admin-123');

        $pending = [
            'name' => 'Api Baru',
            'email' => 'api.baru@contoh.id',
            'password' => 'Password-kuat-123',
            'password_confirmation' => 'Password-kuat-123',
        ];

        $this->postJson('/api/v1/users', $pending, [
            'X-Integration-Key' => 'kunci-integrasi-admin-123',
        ])
            ->assertCreated()
            ->assertJsonPath('data.plan', 'pro');

        $this->assertSame('pro', User::where('email', 'api.baru@contoh.id')->firstOrFail()->plan);
    }

    public function test_config_default_plan_adalah_pro_dan_lewat_factory_pro(): void
    {
        $this->assertSame('pro', config('plans.default'));

        $user = User::factory()->create();

        $this->assertSame('pro', $user->plan);
        $this->assertTrue(Hash::check('password', $user->password));
    }
}
