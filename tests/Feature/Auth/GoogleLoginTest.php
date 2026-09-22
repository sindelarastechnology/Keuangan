<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    private const REDIRECT_URI = 'https://kaspro.sindelarastechnology.my.id/auth/google/callback';

    private function setGoogleConfig(): void
    {
        config(['services.google' => [
            'client_id' => 'client-test.apps.googleusercontent.com',
            'client_secret' => 'GOCSPX-test-secret',
            'redirect' => self::REDIRECT_URI,
        ]]);
    }

    public function test_redirect_mengarahkan_ke_google_dengan_param_oauth_dan_state_di_session(): void
    {
        $this->setGoogleConfig();

        $response = $this->get(route('google.redirect'));

        $location = $response->headers->get('Location');
        $this->assertNotNull($location);
        $this->assertStringStartsWith('https://accounts.google.com/o/oauth2/v2/auth?', $location);

        parse_str((string) parse_url($location, PHP_URL_QUERY), $query);
        $this->assertSame('client-test.apps.googleusercontent.com', $query['client_id']);
        $this->assertSame(self::REDIRECT_URI, $query['redirect_uri']);
        $this->assertSame('code', $query['response_type']);
        $this->assertSame('openid email profile', $query['scope']);
        $this->assertSame('offline', $query['access_type']);
        $this->assertSame($query['state'], session('google_state'));
        $this->assertNotEmpty($query['state']);
    }

    public function test_callback_membuat_akun_baru_login_dan_menyediakan_tenant(): void
    {
        $this->setGoogleConfig();
        Http::preventStrayRequests();

        $googleId = '112345678901234567890';
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'token-abc',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'id_token' => 'id-token',
            ]),
            'www.googleapis.com/oauth2/v2/userinfo' => Http::response([
                'id' => $googleId,
                'name' => 'Budi Google',
                'email' => 'budi@gmail.com',
            ]),
        ]);

        $state = 'state-valid-123';
        $response = $this->withSession(['google_state' => $state])
            ->get(route('google.callback', ['code' => 'kode-auth', 'state' => $state]));

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();

        $user = User::where('email', 'budi@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertSame($googleId, $user->google_id);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->password);
        $this->assertAuthenticatedAs($user);

        $this->assertDatabaseHas('akun_perkiraan', ['user_id' => $user->id]);
    }

    public function test_callback_mengikat_google_id_ke_akun_yang_sudah_terdaftar_via_email(): void
    {
        $this->setGoogleConfig();
        Http::preventStrayRequests();

        $existing = User::factory()->create([
            'email' => 'sudah@gmail.com',
            'email_verified_at' => null,
        ]);

        $googleId = '9988776655';
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response(['access_token' => 'token-abc']),
            'www.googleapis.com/oauth2/v2/userinfo' => Http::response([
                'id' => $googleId,
                'name' => 'Akun Lama',
                'email' => 'sudah@gmail.com',
            ]),
        ]);

        $state = 'state-lama-456';
        $response = $this->withSession(['google_state' => $state])
            ->get(route('google.callback', ['code' => 'kode-auth', 'state' => $state]));

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($existing);

        $existing->refresh();
        $this->assertSame($googleId, $existing->google_id);
        $this->assertNotNull($existing->email_verified_at);

        $this->assertSame(1, User::where('email', 'sudah@gmail.com')->count());
    }

    public function test_callback_mengarahkan_ke_login_saat_state_tidak_bersesuaian(): void
    {
        $this->setGoogleConfig();
        Http::preventStrayRequests();

        $response = $this->withSession(['google_state' => 'state-beda'])
            ->get(route('google.callback', ['code' => 'kode-auth', 'state' => 'state-salah']));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_callback_mengarahkan_ke_login_saat_user_membatalkan(): void
    {
        $this->setGoogleConfig();

        $response = $this->get(route('google.callback', ['error' => 'access_denied']));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $this->assertSame('Login dengan Google dibatalkan. Silakan coba lagi.', session('errors')->first('email'));
    }

    public function test_callback_mengarahkan_ke_login_saat_kode_hilang(): void
    {
        $this->setGoogleConfig();

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_callback_mengarahkan_ke_login_saat_koneksi_ke_google_gagal(): void
    {
        $this->setGoogleConfig();
        Http::preventStrayRequests();
        Http::fake([
            'oauth2.googleapis.com/token' => Http::failedConnection(),
        ]);

        $state = 'state-koneksi-gagal';
        $response = $this->withSession(['google_state' => $state])
            ->get(route('google.callback', ['code' => 'kode-auth', 'state' => $state]));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_callback_mengembalikan_ke_login_saat_tukar_kode_gagal(): void
    {
        $this->setGoogleConfig();
        Http::preventStrayRequests();
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response(['error' => 'invalid_grant'], 400),
        ]);

        $state = 'state-invalid-789';
        $response = $this->withSession(['google_state' => $state])
            ->get(route('google.callback', ['code' => 'kode-buruk', 'state' => $state]));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_callback_mengembalikan_ke_login_saat_ambil_profil_gagal(): void
    {
        $this->setGoogleConfig();
        Http::preventStrayRequests();
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response(['access_token' => 'token-abc']),
            'www.googleapis.com/oauth2/v2/userinfo' => Http::response([], 500),
        ]);

        $state = 'state-profil-gagal';
        $response = $this->withSession(['google_state' => $state])
            ->get(route('google.callback', ['code' => 'kode-auth', 'state' => $state]));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }
}
