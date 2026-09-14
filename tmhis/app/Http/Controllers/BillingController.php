<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('accountant.dashboard');
    }
}
