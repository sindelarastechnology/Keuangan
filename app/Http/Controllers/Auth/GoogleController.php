<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class GoogleController extends Controller
{
    /**
     * Arahkan user ke layar persetujuan Google OAuth 2.0.
     */
    public function redirect(): RedirectResponse
    {
        $state = Str::random(40);
        session(['google_state' => $state]);

        $query = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'offline',
            'prompt' => 'select_account',
        ]);

        return redirect("https://accounts.google.com/o/oauth2/v2/auth?{$query}");
    }

    /**
     * Terima callback dari Google, buat/temukan user, lalu login.
     */
    public function callback(Request $request): RedirectResponse
    {
        // User menolak izin (Google kirim ?error=access_denied) atau kode hilang.
        if ($request->query('error') || ! $request->filled('code')) {
            return $this->loginRedirect('Login dengan Google dibatalkan. Silakan coba lagi.');
        }

        try {
            if (! hash_equals((string) session('google_state', ''), (string) $request->query('state', ''))) {
                return $this->loginRedirect('Sesi login Google kedaluwarsa. Silakan coba lagi.');
            }

            session()->forget('google_state');

            $tokenResponse = Http::connectTimeout(3)
                ->timeout(10)
                ->asForm()
                ->post('https://oauth2.googleapis.com/token', [
                    'code' => $request->query('code'),
                    'client_id' => config('services.google.client_id'),
                    'client_secret' => config('services.google.client_secret'),
                    'redirect_uri' => config('services.google.redirect'),
                    'grant_type' => 'authorization_code',
                ]);

            if ($tokenResponse->failed()) {
                return $this->loginRedirect('Gagal mempertukarkan kode OAuth dengan Google.');
            }

            $profileResponse = Http::connectTimeout(3)
                ->timeout(10)
                ->withToken($tokenResponse->json('access_token'))
                ->get('https://www.googleapis.com/oauth2/v2/userinfo');

            if ($profileResponse->failed()) {
                return $this->loginRedirect('Gagal mengambil profil Google Anda.');
            }

            $profile = $profileResponse->json();
            $googleId = (string) $profile['id'];
            $email = Str::lower($profile['email']);

            $user = User::where('google_id', $googleId)->first()
                ?? User::where('email', $email)->first();

            if ($user) {
                // Ikat google_id ke akun yang sudah terdaftar lewat email yang sama.
                $user->google_id = $googleId;
                $user->email_verified_at ??= now();
                $user->save();
            } else {
                // Email dari Google sudah terverifikasi oleh Google.
                $user = User::create([
                    'name' => $profile['name'] ?? $email,
                    'email' => $email,
                    'google_id' => $googleId,
                    'password' => null,
                    'status' => 'aktif',
                    'plan' => 'pro',
                ]);
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            if (! $user->isAktif()) {
                return $this->loginRedirect('Akun Anda telah dinonaktifkan. Silakan hubungi administrator.');
            }

            Auth::login($user);

            return redirect()->intended(route('dashboard'));
        } catch (Throwable $e) {
            Log::warning('Google OAuth callback gagal.', ['exception' => $e]);

            return $this->loginRedirect('Terjadi kesalahan saat login dengan Google. Silakan coba lagi.');
        }
    }

    private function loginRedirect(string $message): RedirectResponse
    {
        return redirect()
            ->route('login')
            ->withErrors(['email' => $message]);
    }
}
