<?php

namespace App\Http\Middleware;

use App\Services\PlanService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlanFeature
{
    /**
     * Blokir akses bila paket user tidak mencakup fitur yang diminta.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $fitur): Response
    {
        if (! PlanService::can($fitur)) {
            if ($request->expectsJson()) {
                abort(403, 'Fitur ini hanya tersedia untuk paket Pro.');
            }

            return redirect()->route('upgrade.index')
                ->with('error', 'Fitur ini hanya tersedia di paket Pro. Silakan tingkatkan paket Anda terlebih dahulu.');
        }

        return $next($request);
    }
}
