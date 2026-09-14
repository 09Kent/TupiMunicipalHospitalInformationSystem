<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NurseController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('nurse.dashboard.index');
    }
}
