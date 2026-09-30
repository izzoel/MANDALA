<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // Super admin has full access
        if ($user->role === 'super_admin' || $user->hasRole('super_admin')) {
            return $next($request);
        }

        if (empty($roles)) {
            return $next($request);
        }

        if (in_array($user->role, $roles) || $user->hasAnyRole($roles)) {
            return $next($request);
        }

        abort(403, 'Akses ditolak. Peran pengguna ('.$user->role.') tidak memiliki izin untuk halaman ini.');
    }
}
