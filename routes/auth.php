<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/register', function () {
    return view('auth.register');
})
    ->middleware('guest')
    ->name('register');

Route::post('/register', function () {
    // Jetstream handles this via Livewire
    return redirect()->route('register');
})
    ->middleware('guest');

// Login - Keep your existing controller
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

// Logout - Keep your existing controller
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Password Reset - Jetstream provides these views
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', function () {
    // Jetstream handles this via Livewire
    return back();
})
    ->middleware('guest')
    ->name('password.email');

// Email Verification - Using Jetstream's built-in features
Route::get('/verify-email', function () {
    return view('auth.verify-email');
})
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verification-notification', function () {
    auth()->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');