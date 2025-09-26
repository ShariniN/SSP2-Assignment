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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/category/{id}', [HomeController::class, 'category'])->name('category.show');

// Categories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{id}', [CategoryController::class, 'showProducts'])->name('category.products');

// Products (Public - can be viewed without authentication)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.details');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/{id}/quick-view', [ProductController::class, 'quickView'])->name('products.quick-view');

// ------------------- Cart -------------------
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {
        // Cart routes for authenticated users only
        Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
        Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
        Route::get('/cart/count', [CartController::class, 'getCount'])->name('cart.count');
    });


// ------------------- Checkout -------------------
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

// ------------------- Authentication (Jetstream/Fortify) -------------------
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        // Order history for authenticated users
        Route::get('/orders', [CheckoutController::class, 'orders'])->name('orders.index');
        Route::get('/orders/{order}', [CheckoutController::class, 'showOrder'])->name('orders.show');

        Route::middleware('admin')->group(function () {
            Route::get('/admin', function () {
                return view('admin.dashboard');
            })->name('admin.dashboard');
            
            // Admin order management
            Route::get('/admin/orders', [CheckoutController::class, 'adminOrders'])->name('admin.orders');
            Route::patch('/admin/orders/{order}/status', [CheckoutController::class, 'updateOrderStatus'])->name('admin.orders.update-status');
        });
    });