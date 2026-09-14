<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecordsController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('medical_officer.dashboard');
    }
}
