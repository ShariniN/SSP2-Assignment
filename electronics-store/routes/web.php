<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

//Home
Route::get('/', function () {
    return view('home');
})->name('home');  

// Categories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{id}', [CategoryController::class, 'showProducts'])->name('category.products');

// Product Details
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.details');

// Cart
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

Route::get('/search', [ProductController::class, 'search'])->name('search');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');


//Authentication
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


