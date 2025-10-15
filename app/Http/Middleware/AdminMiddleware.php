<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $u = Auth::user();
        if (!$u || (int)$u->role_id !== 2) {
            return redirect('/')->withErrors('Acceso no autorizado.');
        }
        return $next($request);
    }
}
