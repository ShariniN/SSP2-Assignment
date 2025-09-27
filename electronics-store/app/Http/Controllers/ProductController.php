<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)->with('category');

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);
        $categories = Category::where('is_active', true)->get();

        // Get price range for filters
        $priceRange = Product::where('is_active', true)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return view('products.index', compact(
            'products', 
            'categories', 
            'priceRange'
        ));
    }
    
    public function search(Request $request)
    {
        $query = $request->input('q') ?: $request->input('search');
        
        if (empty($query)) {
            return redirect()->route('products.index');
        }

        $products = Product::where('is_active', true)
            ->with('category')
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->latest()
            ->paginate(12)
            ->appends(['q' => $query]);

        $categories = Category::where('is_active', true)->get();

        // Get price range for filters
        $priceRange = Product::where('is_active', true)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return view('products.index', compact('products', 'categories', 'priceRange'));
    }

    public function show($id)
    {
        $product = Product::where('is_active', true)
            ->with(['category'])
            ->findOrFail($id);

        // Get related products from the same category
        $relatedProducts = Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        // Mock data for reviews (since we don't have a Review model yet)
        $averageRating = 4.2; // This should come from actual reviews
        $reviewCount = 15;    // This should come from actual reviews count

        // Get product specifications (assuming you have a specifications JSON field)
        $specifications = $product->specifications ? json_decode($product->specifications, true) : [];

        // Track product view (for analytics)
        $this->trackProductView($product);

        return view('products.show', compact(
            'product',
            'relatedProducts',
            'averageRating',
            'reviewCount',
            'specifications'
        ));
    }

    public function quickView($id)
    {
        $product = Product::where('is_active', true)->findOrFail($id);
        
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'discount_price' => $product->discount_price,
            'description' => $product->description,
            'image' => $product->image ? asset('storage/' . $product->image) : null,
            'stock_quantity' => $product->stock_quantity,
            'sku' => $product->sku,
        ]);
    }

    public function addToWishlist(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        // Get or create wishlist from session
        $wishlist = session()->get('wishlist', []);
        
        if (!in_array($id, $wishlist)) {
            $wishlist[] = $id;
            session()->put('wishlist', $wishlist);
            
            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist!',
                'wishlist_count' => count($wishlist)
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Product already in wishlist!'
        ]);
    }

    public function removeFromWishlist($id)
    {
        $wishlist = session()->get('wishlist', []);
        $wishlist = array_diff($wishlist, [$id]);
        session()->put('wishlist', $wishlist);
        
        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist!',
            'wishlist_count' => count($wishlist)
        ]);
    }

    private function trackProductView($product)
    {
        // Simple view tracking - in production, you might want to use a more sophisticated system
        $viewedProducts = session()->get('viewed_products', []);
        
        // Remove if already exists to avoid duplicates
        $viewedProducts = array_filter($viewedProducts, function($viewed) use ($product) {
            return $viewed['id'] != $product->id;
        });
        
        // Add to beginning of array
        array_unshift($viewedProducts, [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'image' => $product->image,
            'viewed_at' => now()
        ]);
        
        // Keep only last 10 viewed products
        $viewedProducts = array_slice($viewedProducts, 0, 10);
        
        session()->put('viewed_products', $viewedProducts);
    }

    public function compare(Request $request)
    {
        $productIds = $request->get('products', []);
        
        if (empty($productIds)) {
            return redirect()->route('products.index')
                ->with('error', 'Please select products to compare.');
        }
        
        $products = Product::whereIn('id', $productIds)
            ->where('is_active', true)
            ->get();
        
        if ($products->count() < 2) {
            return redirect()->route('products.index')
                ->with('error', 'Please select at least 2 products to compare.');
        }
        
        return view('products.compare', compact('products'));
    }
}