<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function google_redirect()
    {
        return Socialite::driver('google')->redirect();
    }
    public function google_callback()
    {
        /** @var \Laravel\Socialite\Contracts\User $googleUser */
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::whereEmail($googleUser->email)->first();

        if (!$user) {
            return redirect('/login')->with('failed', 'Email Anda tidak terdaftar di sistem.');
        }

        if ($user->status == 'banded') {
            return redirect('/login')->with('failed', 'Akun Anda telah dibekukan.');
        }

        if ($user->status == 'verify') {
            $user->update(['status' => 'aktif']);
        }

        Auth::login($user);
        session(['active_role' => $user->id_role]);
        switch ($user->id_role) {
            case 1:
                return redirect()->route('adminsdm.dashboard');
            case 2:
                return redirect()->route('supervisor.spv-dashboard');
            case 3:
                return redirect()->route('mentor.dashboard-mentor');
            case 4:
                return redirect()->route('karyawan.dashboard-karyawan');
            default:
                return redirect('/');
        }
    }
}
