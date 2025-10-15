<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Ajusta a tu lógica: tienes role_id y método isRole('admin').
        if (!$user || !$user->isRole('admin')) {
            abort(403); // o return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
