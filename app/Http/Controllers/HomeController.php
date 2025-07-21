<?php

namespace LaravelApp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $authType = config('auth.type', env('AUTH', 'none'));
        
        // If user is authenticated, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('welcome', compact('authType'));
    }
}
