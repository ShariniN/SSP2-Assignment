<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Http\Requests\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request)
    {
        // Store the intended URL if it exists
        if ($request->has('redirect_to')) {
            $request->session()->put('url.intended', $request->redirect_to);
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Get the intended URL or default to home
        $intendedUrl = $request->session()->get('url.intended');
        
        if ($intendedUrl) {
            $request->session()->forget('url.intended');
            return redirect($intendedUrl);
        }

        // Default redirect based on user role or to home
        return redirect()->route('home');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}