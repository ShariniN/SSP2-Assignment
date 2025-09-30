<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Admin\AdminController;

// ------------------- Social Login -------------------
Route::get('/login/google', [SocialLoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/login/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);

// ------------------- Public Pages -------------------
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/category/{id}', [HomeController::class, 'category'])->name('category.show');

// Categories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{id}', [CategoryController::class, 'showProducts'])->name('category.products');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/{id}/quick-view', [ProductController::class, 'quickView'])->name('products.quick-view');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

// Wishlist (Web)
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add/{product}', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');
});

// Cart (Web)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/cart', function () {
        return view('cart-page'); // wrapper Blade file
    })->name('cart.index');
});

// Checkout (Web)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

    // Orders for regular users
    Route::get('/orders', [CheckoutController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{order}', [CheckoutController::class, 'showOrder'])->name('orders.show');
    Route::get('/orders/{order}/json', [AdminController::class, 'getOrderJson']);
    Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.update-status');
    Route::delete('/orders/{order}', [AdminController::class, 'deleteOrder'])->name('admin.orders.delete');
});

// ------------------- Admin Dashboard & Management -------------------
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // Products
        Route::get('/products', [AdminController::class, 'products'])->name('products.index');
        Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
        Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('products.delete');
        Route::get('/products/{product}', [AdminController::class, 'showProduct'])->name('products.show');

        // Categories
        Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
        Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory'])->name('categories.delete');
        Route::get('/categories/{category}/edit-json', [AdminController::class, 'getCategoryJson'])->name('categories.json');

        // Orders
        Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
        Route::get('/orders/{order}/json', [AdminController::class, 'getOrderJson'])->name('orders.json');
        Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.update-status');
        Route::delete('/orders/{order}', [AdminController::class, 'deleteOrder'])->name('orders.delete');

        // Users
        Route::get('/users', [AdminController::class, 'indexUsers'])->name('users.index');
        Route::get('/users/{user}/json', [AdminController::class, 'getUserJson'])->name('users.json');
        Route::patch('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        // Wishlists
        Route::get('/wishlists', [AdminController::class, 'indexWishlists'])->name('wishlists.index');
        Route::get('/users/{user}/wishlist/json', [AdminController::class, 'getWishlistJson'])->name('wishlists.json');
        Route::delete('/wishlists/{user}/{product}', [AdminController::class, 'removeWishlistItem'])->name('wishlists.remove');
    });

// ------------------- Login (Web) -------------------
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ------------------- API Routes for Mobile App -------------------
Route::prefix('api')->middleware('auth:sanctum')->group(function () {

    // Authentication
    Route::post('/login', [AuthenticatedSessionController::class, 'apiLogin']);
    Route::post('/register', [AuthenticatedSessionController::class, 'apiRegister']);
    Route::get('/user', [AuthenticatedSessionController::class, 'apiUser']);

    // Products
    Route::get('/products', [AdminController::class, 'apiProducts']);
    Route::get('/products/{product}', [AdminController::class, 'showProduct']); // detail

    // Categories
    Route::get('/categories', [AdminController::class, 'apiCategories']);

    // Brands
    Route::get('/brands', [AdminController::class, 'apiBrands']);

    // Orders
    Route::get('/orders', [CheckoutController::class, 'apiUserOrders']);
    Route::get('/orders/{order}', [AdminController::class, 'getOrderJson']);

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'apiIndex']);
    Route::post('/wishlist/add/{product}', [WishlistController::class, 'add']);
    Route::delete('/wishlist/remove/{product}', [WishlistController::class, 'remove']);

    // Cart / Checkout
    Route::get('/cart', [CheckoutController::class, 'apiCart']);
    Route::post('/cart/add/{product}', [CheckoutController::class, 'apiAddToCart']);
    Route::post('/checkout', [CheckoutController::class, 'apiCheckout']);
    Route::get('/checkout/success', [CheckoutController::class, 'success']);
});
