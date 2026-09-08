<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperadmin
{
    /**
     * Handle an incoming request.
     * Aborts with 403 unless the authenticated user is a superadmin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->is_superadmin) {
            abort(403, 'Unauthorized. Halaman ini hanya untuk superadmin.');
        }

        return $next($request);
    }
}
