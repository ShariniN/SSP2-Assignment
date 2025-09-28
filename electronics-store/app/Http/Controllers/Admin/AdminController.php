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

class AdminController extends Controller
{
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

    // Products Management
    public function products()
    {
        $products = Product::with(['category', 'brand'])->latest()->paginate(15);
        $categories = Category::all();
        $brands = Brand::all() ?? collect();
        return view('admin.products', compact('products', 'categories', 'brands'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sku' => 'nullable|string|unique:products',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'nullable|json',
            'is_active' => 'boolean',
            'is_featured' => 'boolean'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'nullable|json',
            'is_active' => 'boolean',
            'is_featured' => 'boolean'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function deleteProduct(Product $product)
    {
        $hasOrders = OrderItem::where('product_id', $product->id)->exists();
        
        if ($hasOrders) {
            return redirect()->route('admin.products.index')->with('error', 'Cannot delete product with existing orders.');
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    // Categories Management
    public function categories()
    {
        $categories = Category::withCount('products')->latest()->paginate(15);
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

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

    // Orders Management
    public function orders()
    {
        $orders = Order::with(['user', 'items'])->latest()->paginate(15);
        return view('admin.orders', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,completed'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    public function deleteOrder(Order $order)
    {
        if (!in_array($order->status, ['cancelled', 'completed'])) {
            return redirect()->route('admin.orders.index')->with('error', 'Only cancelled or completed orders can be deleted.');
        }

        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }

    // Users Management
    public function users()
    {
        $users = User::where('is_admin', false)->latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function toggleUserStatus(User $user)
    {
        if ($user->banned_at) {
            $user->update(['banned_at' => null]);
            $message = 'User activated successfully.';
        } else {
            $user->update(['banned_at' => now()]);
            $message = 'User banned successfully.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function deleteUser(User $user)
    {
        if ($user->orders()->count() > 0) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete user with existing orders.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    // Wishlists Management
    public function wishlists()
    {
        // Get users who have wishlist items
        $users = User::whereHas('wishlist')
                    ->withCount('wishlist')
                    ->latest()
                    ->paginate(15);

        return view('admin.wishlists', compact('users'));
    }

    public function showUserWishlist(User $user)
    {
        $wishlistItems = Wishlist::where('user_id', $user->id)
                                ->with('product')
                                ->latest()
                                ->paginate(15);

        return view('admin.wishlists.show', compact('user', 'wishlistItems'));
    }

    public function removeWishlistItem(User $user, $productId)
    {
        $wishlist = Wishlist::where('user_id', $user->id)
                           ->where('product_id', $productId)
                           ->first();

        if ($wishlist) {
            $wishlist->delete();
            return redirect()->back()->with('success', 'Item removed from wishlist successfully.');
        }

        return redirect()->back()->with('error', 'Item not found in wishlist.');
    }

    // Brands Management (if Brand model exists)
    public function brands()
    {
        $brands = Brand::withCount('products')->latest()->paginate(15);
        return view('admin.brands', compact('brands'));
    }

    public function storeBrand(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

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
            'is_active' => 'boolean'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

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