<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            // Save intended URL if user was trying to visit a protected page
            if (url()->previous() && url()->previous() !== url()->current()) {
                session(['url.intended' => url()->previous()]);
            } else {
                // fallback: go to home if there is no meaningful previous page
                session(['url.intended' => route('home')]);
            }

            return route('login');
        }

        return null;
    }
}
