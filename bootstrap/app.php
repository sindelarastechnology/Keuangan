<?php

use App\Http\Middleware\Api\AuthenticateIntegrationKey;
use App\Http\Middleware\EnsurePlanFeature;
use App\Http\Middleware\PastikanAkunAktif;
use App\Http\Middleware\ResolveTenantDatabase;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'akun-aktif' => PastikanAkunAktif::class,
            'auth.integration' => AuthenticateIntegrationKey::class,
            'plan.feature' => EnsurePlanFeature::class,
            'tenant-db' => ResolveTenantDatabase::class,
        ]);

        // Route model binding (SubstituteBindings, bagian grup `web`) harus
        // berjalan SETELAH koneksi tenant diarahkan. Tanpa urutan ini, model
        // di-resolve pada database shared (mysql) yang tidak punya data tenant,
        // sehingga halaman detail/preview transaksi selalu 404.
        $middleware->prependToPriorityList(
            SubstituteBindings::class,
            ResolveTenantDatabase::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
