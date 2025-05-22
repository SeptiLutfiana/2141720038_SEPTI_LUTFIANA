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
    public function google_redirect(){
        return Socialite::driver('google')->redirect();
    }
    public function google_callback(){
        $googleUser = Socialite::driver('google')->user();
        $user = User::whereEmail($googleUser->email)->first();
        if(!$user){
            $user = User::create(['name'=>$googleUser->name, 'email' =>$googleUser->email, 'status'=>'aktif']);
        }
        if($user && $user->status == 'banded'){
            return redirect('/login')->with('failed', 'Akun Anda telah dibekukan');
        }
        if($user&& $user->status == 'verify'){
            $user->update(['status' => 'aktif']);
        }
        Auth::login($user);
        if($user->id_role == 1){ 
            return redirect('/adminsdm-dashboard');
        } elseif ($user->id_role == 2) {
            return redirect('/supervisor-dashboard');
        } elseif ($user->id_role == 3) {
            return redirect('/mentor-dashboard');
        } elseif ($user->id_role == 4) {
            return redirect('/karyawan-dashboard');
        } else {
            return redirect('/'); // default jika tidak cocok
            }
    }
}
