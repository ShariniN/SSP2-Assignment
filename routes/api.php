<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// ✅ PUBLIC API routes (no authentication required)
// Products - Make these public so Flutter can access without login
Route::get('/products', [ProductController::class, 'apiIndex']);       
Route::get('/products/{id}', [ProductController::class, 'apiShow']);

// Categories - Make these public too
Route::get('/categories', [CategoryController::class, 'apiIndex']);                 
Route::get('/categories/{id}/products', [CategoryController::class, 'apiProducts']); 
Route::get('/categories/search', [CategoryController::class, 'apiSearch']);          
Route::get('/categories/slug/{slug}', [CategoryController::class, 'apiShowBySlug']); 

// Protected API routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);

    //Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist/add', [WishlistController::class, 'add']);
    Route::delete('/wishlist/remove/{productId}', [WishlistController::class, 'remove']);

    //Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::delete('/cart/clear', [CartController::class, 'clear']); 
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
});