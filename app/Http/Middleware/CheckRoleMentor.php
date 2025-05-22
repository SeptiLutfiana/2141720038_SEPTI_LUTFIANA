<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRoleMentor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
{
    if (Auth::check() && Auth::user()->id_role == 3) {
        return $next($request); // Lanjut ke route, karena role benar
    }

    abort(403, 'Unauthorized'); // Atau redirect ke halaman lain
}
}
