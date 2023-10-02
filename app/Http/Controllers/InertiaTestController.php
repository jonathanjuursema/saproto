<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InertiaTestController extends Controller
{
    public function show()
    {
        return Inertia::render('Settings', [
            'user' => Auth::user(),
        ]);
    }

}