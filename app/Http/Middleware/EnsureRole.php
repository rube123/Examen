<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user || !optional($user->role)->name || $user->role->name !== $role) {
            abort(403, 'No autorizado.');
        }

        return $next($request);
    }
}
