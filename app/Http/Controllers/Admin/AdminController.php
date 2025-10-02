<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // ------------------- Dashboard -------------------
    public function dashboard()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'total_users' => User::where('is_admin', false)->count(),
            'total_wishlists' => Wishlist::distinct('user_id')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'recent_orders' => Order::with('user')->latest()->take(5)->get(),
            'top_products' => Product::withCount('orderItems')
                ->orderBy('order_items_count', 'desc')
                ->take(5)
                ->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

// ------------------- Wishlists -------------------
public function indexWishlists(Request $request)
{
    // Get unique user IDs from MongoDB wishlists (they're stored as integers)
    // Using get() and pluck instead of distinct() for MongoDB compatibility
    $userIds = Wishlist::all()->pluck('user_id')->unique()->toArray();

    // Get users from MySQL with pagination
    $users = User::whereIn('id', $userIds)
        ->latest()
        ->paginate(15);

    // Load wishlist data for each user from MongoDB
    $users->getCollection()->transform(function ($user) {
        // Get wishlist items from MongoDB - user_id is stored as integer
        $wishlistItems = Wishlist::where('user_id', $user->id)->get();
        
        \Log::info("Loading wishlist for user {$user->id}", [
            'wishlist_count' => $wishlistItems->count()
        ]);
        
        // Load product data for each wishlist item
        $wishlistWithProducts = collect();
        
        foreach ($wishlistItems as $item) {
            try {
                // Convert product_id string to integer for MySQL query
                $productId = is_string($item->product_id) 
                    ? (int)trim($item->product_id, '"') 
                    : (int)$item->product_id;
                
                $product = Product::with('category')->find($productId);
                
                if ($product) {
                    $item->product = $product;
                    $wishlistWithProducts->push($item);
                } else {
                    \Log::warning("Product not found for wishlist item", [
                        'product_id_original' => $item->product_id,
                        'product_id_converted' => $productId
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error("Error loading product for wishlist item", [
                    'error' => $e->getMessage(),
                    'product_id' => $item->product_id ?? 'unknown'
                ]);
            }
        }
        
        // Attach the wishlist collection to the user object
        $user->wishlist = $wishlistWithProducts;
        
        \Log::info("Loaded wishlist products for user {$user->id}", [
            'products_loaded' => $wishlistWithProducts->count()
        ]);
        
        return $user;
    });

    return view('admin.wishlists', compact('users'));
}

public function getWishlistJson(User $user)
{
    // Get wishlist items from MongoDB - user_id is stored as integer
    $wishlistItems = Wishlist::where('user_id', $user->id)->get();

    \Log::info("Getting wishlist JSON for user {$user->id}", [
        'items_found' => $wishlistItems->count()
    ]);

    // Load products from MySQL
    $wishlistWithProducts = collect();
    
    foreach ($wishlistItems as $item) {
        try {
            // Convert product_id string to integer for MySQL query
            $productId = is_string($item->product_id) 
                ? (int)trim($item->product_id, '"') 
                : (int)$item->product_id;
            
            $product = Product::with('category')->find($productId);
            
            if ($product) {
                $wishlistWithProducts->push([
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'product_id' => $productId,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                    'product' => $product->toArray()
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Error loading product for wishlist JSON", [
                'error' => $e->getMessage(),
                'product_id' => $item->product_id ?? 'unknown'
            ]);
        }
    }

    return response()->json([
        'user' => $user,
        'wishlist' => $wishlistWithProducts,
    ]);
}

public function removeWishlistItem(User $user, $productId)
{
    // Handle both string and integer product IDs
    $productIdString = (string)$productId;
    $productIdQuoted = '"' . $productId . '"';
    
    \Log::info("Attempting to remove wishlist item", [
        'user_id' => $user->id,
        'product_id' => $productId
    ]);
    
    // Try to delete with multiple formats
    $deleted = Wishlist::where('user_id', $user->id)
        ->where(function($query) use ($productId, $productIdString, $productIdQuoted) {
            $query->where('product_id', (int)$productId)
                  ->orWhere('product_id', $productIdString)
                  ->orWhere('product_id', $productIdQuoted);
        })
        ->delete();

    if ($deleted) {
        \Log::info("Wishlist item removed successfully", [
            'user_id' => $user->id,
            'product_id' => $productId,
            'deleted_count' => $deleted
        ]);
        return redirect()->back()->with('success', 'Item removed from wishlist successfully.');
    }

    \Log::warning("Wishlist item not found for removal", [
        'user_id' => $user->id,
        'product_id' => $productId
    ]);
    
    return redirect()->back()->with('error', 'Item not found in wishlist.');
}

    // ------------------- Products -------------------
    public function products()
    {
        $products = Product::with(['category', 'brand'])->latest()->paginate(15);
        $categories = Category::all();
        $brands = Brand::all() ?? collect();
        return view('admin.products', compact('products', 'categories', 'brands'));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'sku' => 'nullable|string|unique:products',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'nullable|string'
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'discount_price' => $validated['discount_price'] ?? null,
            'stock_quantity' => $validated['stock_quantity'],
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
            'sku' => $validated['sku'] ?? strtoupper(Str::random(8)),
            'is_active' => $request->has('is_active') ? 1 : 0,
            'is_featured' => $request->has('is_featured') ? 1 : 0
        ];

        // handle specs
        if (!empty($validated['specifications'])) {
            $decoded = json_decode($validated['specifications'], true);
            $data['specifications'] = $decoded ?? [];
        } else {
            $data['specifications'] = [];
        }

        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'stock_quantity' => 'required|integer',
            'description' => 'required|string',
            'specifications' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
            'price' => $validated['price'],
            'discount_price' => $validated['discount_price'] ?? null,
            'stock_quantity' => $validated['stock_quantity'],
            'description' => $validated['description'],
            'is_active' => $request->has('is_active') ? 1 : 0,
            'is_featured' => $request->has('is_featured') ? 1 : 0
        ];

        if (!empty($validated['specifications'])) {
            $decoded = json_decode($validated['specifications'], true);
            $data['specifications'] = $decoded ?? [];
        } else {
            $data['specifications'] = [];
        }

        if ($request->hasFile('image')) {
            if ($product->image_url) {
                Storage::disk('public')->delete($product->image_url);
            }
            $data['image_url'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function deleteProduct(Product $product)
    {
        if (OrderItem::where('product_id', $product->id)->exists()) {
            return redirect()->route('admin.products.index')->with('error', 'Cannot delete product with existing orders.');
        }

        if ($product->image_url) {
            Storage::disk('public')->delete($product->image_url);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    public function showProduct(Product $product)
    {
        $product->load('category', 'brand');
        return response()->json($product);
    }

    // ------------------- Users -------------------
    public function indexUsers(Request $request)
    {
        $query = User::withCount('orders')->where('is_admin', false);

        if ($request->status === 'active') {
            $query->whereNull('banned_at');
        } elseif ($request->status === 'banned') {
            $query->whereNotNull('banned_at');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function getUserJson(User $user)
    {
        $user->load('orders');
        return response()->json($user);
    }

    public function toggleUserStatus(User $user)
    {
        $user->banned_at = $user->banned_at ? null : now();
        $user->save();

        return redirect()->back()->with('success', 'User status updated.');
    }

    public function destroyUser(User $user)
    {
        if ($user->orders()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete a user with orders.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    // ------------------- Orders -------------------
    public function orders()
    {
        $orders = Order::with(['user', 'items.product'])->latest()->paginate(15);
        return view('admin.orders', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function getOrderJson(Order $order)
    {
        $order->load('user', 'items.product');
        return response()->json($order);
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);
        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    public function deleteOrder(Order $order)
    {
        $order->delete();
        return redirect()->back()->with('success', 'Order deleted successfully.');
    }

    // ------------------- Categories -------------------
    public function categories()
    {
        $categories = Category::withCount('products')->latest()->paginate(15);
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string'
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active') ? 1 : 0
        ];

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string'
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active') ? 1 : 0
        ];

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function deleteCategory(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Cannot delete category with existing products.');
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }

    public function getCategoryJson(Category $category)
    {
        return response()->json($category);
    }

    // ------------------- Brands -------------------
    public function brands()
    {
        $brands = Brand::withCount('products')->latest()->paginate(15);
        return view('admin.brands', compact('brands'));
    }

    public function storeBrand(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active') ? 1 : 0
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('brands', 'public');
        }

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully.');
    }

    public function updateBrand(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean'
        ]);

        $data = $request->only(['name','description']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        if ($request->hasFile('image')) {
            if ($brand->image) {
                Storage::disk('public')->delete($brand->image);
            }
            $data['image'] = $request->file('image')->store('brands', 'public');
        }

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully.');
    }

    public function deleteBrand(Brand $brand)
    {
        if ($brand->products()->count() > 0) {
            return redirect()->route('admin.brands.index')->with('error', 'Cannot delete brand with existing products.');
        }

        if ($brand->image) {
            Storage::disk('public')->delete($brand->image);
        }

        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully.');
    }
}