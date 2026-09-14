<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('pharmacy.dashboard.index');
    }
}
