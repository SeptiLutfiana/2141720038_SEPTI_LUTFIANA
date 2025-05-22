<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('adminsdm.dashboard', [
            'type_menu' => 'dashboard',
        ]);
    }
}
