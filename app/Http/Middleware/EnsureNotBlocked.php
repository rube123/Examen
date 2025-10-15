<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotBlocked
{
    /**
     * Si el usuario está bloqueado (blocked_at != null) devolvemos 403.
     * Este middleware debe ejecutarse DESPUÉS de 'auth'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->blocked_at) {
            // 403 Forbidden con mensaje claro.
            abort(403, 'Tu cuenta está bloqueada por la administración.');
        }

        return $next($request);
    }
}
