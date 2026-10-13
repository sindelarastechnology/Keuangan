<?php

namespace App\Http\Middleware;

use App\Services\TenantDatabase;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Arahkan default connection ke database tenant milik user yang login.
 *
 * Ketika fitur dinonaktifkan (mis. saat test suite) atau user belum login,
 * middleware ini tidak melakukan apa pun dan aplikasi berjalan pada database
 * shared seperti sebelumnya.
 */
class ResolveTenantDatabase
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! TenantDatabase::enabled() || ! Auth::check()) {
            return $next($request);
        }

        TenantDatabase::ensure(Auth::user());

        try {
            return $next($request);
        } finally {
            TenantDatabase::disconnect();
        }
    }
}
