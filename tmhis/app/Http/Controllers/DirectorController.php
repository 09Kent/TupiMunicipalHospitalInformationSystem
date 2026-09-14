<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DirectorController extends Controller
{
    public function dashboard()
    {
        $user = [
            'name' => session('full_name', auth()->user()->FirstName ?? 'Dr. Maria Santos'),
            'role' => session('role', 'Director'),
        ];

        return view('cmo.dashboard', compact('user'));
    }
}
