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
use App\Models\Wishlist;
use App\Models\Product;

Route::get('/test-csrf', function () {
    return [
        'csrf_token' => csrf_token(),
        'session_id' => session()->getId(),
        'session_works' => session()->has('_token'),
    ];
});


// ------------------- Social Login -------------------
Route::get('/auth/google', [SocialLoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);

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

// Review Routes (Protected - Require Authentication)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/products/{product}/reviews/create', [App\Http\Controllers\ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/products/{product}/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}/edit', [App\Http\Controllers\ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Public Review Routes
Route::get('/products/{product}/reviews', [App\Http\Controllers\ReviewController::class, 'index'])->name('reviews.index');
Route::get('/products/{product}/reviews/load-more', [App\Http\Controllers\ReviewController::class, 'loadMore'])->name('reviews.load-more');

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

        // Brands
        Route::get('/brands', [AdminController::class, 'brands'])->name('brands.index');
        Route::post('/brands', [AdminController::class, 'storeBrand'])->name('brands.store');
        Route::put('/brands/{brand}', [AdminController::class, 'updateBrand'])->name('brands.update');
        Route::delete('/brands/{brand}', [AdminController::class, 'deleteBrand'])->name('brands.delete');

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

        // DEBUG ROUTES - Remove these after testing
        Route::get('/debug-wishlist', function() {
            try {
                $wishlists = Wishlist::all();
                $sampleWishlist = $wishlists->first();
                
                $debugData = [
                    'total_count' => $wishlists->count(),
                    'sample_wishlist' => $sampleWishlist ? $sampleWishlist->toArray() : null,
                    'all_user_ids' => $wishlists->pluck('user_id')->unique()->values()->toArray(),
                    'all_product_ids' => $wishlists->pluck('product_id')->toArray(),
                ];
                
                // Try to load a product if we have a sample
                if ($sampleWishlist) {
                    $productId = is_string($sampleWishlist->product_id) 
                        ? (int)trim($sampleWishlist->product_id, '"') 
                        : (int)$sampleWishlist->product_id;
                    
                    $product = Product::find($productId);
                    
                    $debugData['sample_product_id_original'] = $sampleWishlist->product_id;
                    $debugData['sample_product_id_converted'] = $productId;
                    $debugData['sample_product_found'] = $product ? true : false;
                    $debugData['sample_product_data'] = $product ? $product->toArray() : null;
                }
                
                return response()->json($debugData, 200, [], JSON_PRETTY_PRINT);
                
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        })->name('debug.wishlist');
        
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
require __DIR__.'/auth.php';