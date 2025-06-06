<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRoleKaryawan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
{
    if (Auth::check()){
        if (in_array(Auth::user()->id_role, $roles)) {
                return $next($request); // Lanjutkan request jika role cocok
            }
    }

    abort(403, 'Unauthorized'); // Atau redirect ke halaman lain
}
}
