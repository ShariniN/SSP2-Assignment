<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Auth\SocialLoginController;

// Redirect to provider
Route::get('/login/google', [SocialLoginController::class, 'redirectToGoogle'])->name('login.google');

// Callback from provider
Route::get('/login/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);

// ------------------- Home & Search -------------------
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/category/{slug}', [HomeController::class, 'category'])->name('category.show');

// ------------------- Categories -------------------
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{id}', [CategoryController::class, 'showProducts'])->name('category.products');

// ------------------- Products -------------------
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.details');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');

// ------------------- Cart -------------------
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

// ------------------- Checkout -------------------
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

// ------------------- Authentication (Jetstream/Fortify) -------------------
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        Route::middleware('admin')->group(function () {
            Route::get('/admin', function () {
                return view('admin.dashboard');
            })->name('admin.dashboard');
        });
    });
