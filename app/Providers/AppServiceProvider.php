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
    }

    private function registerEventListeners(): void
    {
        Event::listen(Login::class, ProvisionTenantOnAuth::class);
        Event::listen(Verified::class, ProvisionTenantOnAuth::class);
    }
}
