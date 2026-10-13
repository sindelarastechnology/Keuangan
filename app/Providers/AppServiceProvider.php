<?php

namespace App\Providers;

use App\Listeners\ProvisionTenantOnAuth;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Verified;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRateLimiters();
        $this->registerEventListeners();
    }

    private function registerRateLimiters(): void
    {
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Brute force pada form login: batasi per pasangan email+IP supaya satu
        // akun tidak bisa dibombardir dari banyak IP, dan per IP supaya satu
        // mesin tidak bisa menyapu banyak akun dengan email berbeda.
        RateLimiter::for('login', function (Request $request) {
            $email = mb_strtolower(trim((string) $request->input('email')));

            return [
                Limit::perMinute(5)->by('login:email-ip:'.sha1($email.'|'.$request->ip())),
                Limit::perMinute(20)->by('login:ip:'.$request->ip()),
            ];
        });
    }

    private function registerEventListeners(): void
    {
        Event::listen(Login::class, ProvisionTenantOnAuth::class);
        Event::listen(Verified::class, ProvisionTenantOnAuth::class);
    }
}
