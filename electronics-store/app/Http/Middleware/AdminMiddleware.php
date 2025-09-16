<?php

// app/Http/Middleware/AdminMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
    if (auth()->check() && auth()->user()->role === 'admin') {
        return $next($request);
    }
    return redirect('/dashboard')->with('error', 'Access denied.');
    }

}
