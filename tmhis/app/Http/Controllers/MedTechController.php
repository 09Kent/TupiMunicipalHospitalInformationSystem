<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MedTechController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('medtech.dashboard.index');
    }
}
