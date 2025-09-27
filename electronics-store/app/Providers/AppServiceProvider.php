<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // ← Add this
use Illuminate\Support\Facades\Auth; // ← And this
use App\Models\Cart;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Make $cartCount available to all views
        View::composer('*', function ($view) {
            $cartCount = Auth::check() 
                ? Cart::where('user_id', Auth::id())->count() 
                : (session('cart') ? count(session('cart')) : 0);
            $view->with('cartCount', $cartCount);
        });
    }
}
