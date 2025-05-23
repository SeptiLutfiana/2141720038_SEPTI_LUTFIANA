<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input login dan password
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Tentukan apakah login pakai email atau NPK
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'npk';

        $credentials = [
            $loginType => $request->login,
            'password' => $request->password,
        ];

        // Proses autentikasi
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Ambil semua role user (diasumsikan dari relasi)
            $roles = $user->roles->pluck('id_role')->toArray(); // relasi many-to-many: user->roles

            // Urutan prioritas role
            $prioritas = [1, 2, 3, 4];

            // Ambil role dengan prioritas tertinggi
            $role_aktif = collect($prioritas)->first(function ($role) use ($roles) {
                return in_array($role, $roles);
            });

            // Jika role tidak valid, logout dan beri pesan error
            if (!$role_aktif) {
                Auth::logout();
                return back()->withErrors([
                    'login' => 'Akun Anda tidak memiliki akses yang valid.',
                ])->onlyInput('login');
            }

            // Simpan role aktif di session
            session(['active_role' => $role_aktif]);

            // Redirect sesuai role aktif
            return match ($role_aktif) {
                1 => redirect()->intended('/admin/dashboard'),
                2 => redirect()->intended('/supervisor/dashboard'),
                3 => redirect()->intended('/mentor/dashboard'),
                4 => redirect()->intended('/karyawan/dashboard'),
                default => back(), // Fallback jika role tidak ditemukan
            };
        }

        // Jika gagal login
        return back()->withErrors([
            'login' => 'Login gagal. Periksa kembali email/NPK dan password.',
        ])->onlyInput('login');
    }
    public function switch(Request $request)
{
    $user = Auth::user();
    $newRole = $request->input('role');

    // Ambil semua role yang dimiliki oleh pengguna
    $userRoles = $user->roles->pluck('id_role')->toArray(); // Mengambil id_role dari relasi many-to-many

    // Cek jika role yang dipilih ada di dalam role pengguna
    if (!in_array($newRole, $userRoles)) {
        // Jika role tidak ada dalam role pengguna, tampilkan pesan error dan tetap di halaman yang sama
        return redirect()->back()->with('error', 'Kamu tidak memiliki izin untuk mengakses peran ini. Butuh bantuan? Hubungi administrator ya!');
    }

    // Pastikan 'id_role' adalah kolom yang valid di tabel 'users'
    $user->id_role = $newRole;

    // Cek jika user adalah instansi dari Model Eloquent
    if ($user instanceof \Illuminate\Database\Eloquent\Model) {
        $user->save();
    } else {
        // Tangani jika user bukan model Eloquent
        return redirect()->route('dashboard')->with('error', 'User tidak ditemukan atau bukan model yang valid');
    }
    session(['active_role' => (int)$newRole]);
    // Redirect sesuai dengan role
    switch ($newRole) {
        case 1:
            return redirect()->route('adminsdm.dashboard');
        case 2:
            return redirect()->route('supervisor.spv-dashboard');
        case 3:
            return redirect()->route('mentor.dashboard-mentor');
        case 4:
            return redirect()->route('karyawan.dashboard-karyawan');
        default:
            return redirect()->back();
    }
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}