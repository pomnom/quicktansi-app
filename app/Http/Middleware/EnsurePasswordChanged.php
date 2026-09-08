<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Redirect users who still have the default (NIP) password to their
     * profile page until they set their own password. Profile routes and
     * logout are exempted so the user can actually change it and sign out.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password
            && !$request->routeIs('profile.*')
            && !$request->routeIs('logout')) {
            return redirect()->route('profile.show')
                ->with('warning', 'Anda masih menggunakan password default (NIP). Silakan ganti password Anda terlebih dahulu.');
        }

        return $next($request);
    }
}
