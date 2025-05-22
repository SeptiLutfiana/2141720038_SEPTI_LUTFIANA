<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Http\Request;  // Pastikan Request digunakan dengan benar

class LoginController extends Controller
{
    /**
     * Menangani login pengguna
     */
    public function login(LoginRequest $request)
{
    // Cek apakah terlalu banyak percobaan login
    $this->ensureIsNotRateLimited($request);

    // Jalankan autentikasi dari LoginRequest
    $request->authenticate();

    // Reset hitungan rate limit setelah berhasil login
    RateLimiter::clear($this->throttleKey($request));

    // Ambil user yang baru login
    $user = Auth::user();

    // Ambil roles yang dimiliki user
    $roles = $user->roles;

    // Simpan role aktif ke dalam session
    session(['current_role' => $roles->first()->name]);

    // Arahkan ke halaman dashboard
    return redirect()->route('dashboard');
}


    /**
     * Pastikan login request tidak dibatasi (rate-limited)
     */
    public function ensureIsNotRateLimited(Request $request)  // Pastikan Request digunakan dengan benar
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            event(new Lockout($request));
            $seconds = RateLimiter::availableIn($this->throttleKey($request));

            throw ValidationException::withMessages([
                'login' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }
    }

    /**
     * Ambil throttle key untuk rate limiting
     */
    protected function throttleKey(Request $request): string  // Pastikan Request digunakan dengan benar
    {
        return Str::transliterate(Str::lower($request->input('login')) . '|' . $request->ip());
    }
}
