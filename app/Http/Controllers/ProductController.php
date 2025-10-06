<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)->with('category');

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('id', $request->category);
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

        return view('products.index', compact('products', 'categories', 'priceRange'));
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

        $priceRange = Product::where('is_active', true)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return view('products.index', compact('products', 'categories', 'priceRange'));
    }

    public function show($id)
    {
        $product = Product::where('is_active', true)
            ->with(['category', 'reviews.user'])
            ->findOrFail($id);

        $relatedProducts = Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        $reviewCount = $product->reviews->count();
        $averageRating = $reviewCount > 0 ? $product->reviews->avg('rating') : 0;

        $specifications = is_string($product->specifications)
            ? json_decode($product->specifications, true) ?? []
            : ($product->specifications ?? []);

        $this->trackProductView($product);

        return view('products.show', compact(
            'product', 'relatedProducts', 'averageRating', 'reviewCount', 'specifications'
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
            'image_url' => $product->image ? url($product->image) : null,
            'stock_quantity' => $product->stock_quantity,
            'sku' => $product->sku,
        ]);
    }

    public function addToWishlist(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $wishlist = session()->get('wishlist', []);
        
        if (!in_array($id, $wishlist)) {
            $wishlist[] = $id;
            session()->put('wishlist', $wishlist);
            return response()->json(['success' => true, 'message' => 'Product added!', 'wishlist_count' => count($wishlist)]);
        }
        
        return response()->json(['success' => false, 'message' => 'Product already in wishlist!']);
    }

    public function removeFromWishlist($id)
    {
        $wishlist = session()->get('wishlist', []);
        $wishlist = array_diff($wishlist, [$id]);
        session()->put('wishlist', $wishlist);
        
        return response()->json(['success' => true, 'message' => 'Product removed!', 'wishlist_count' => count($wishlist)]);
    }

    private function trackProductView($product)
    {
        $viewedProducts = session()->get('viewed_products', []);
        $viewedProducts = array_filter($viewedProducts, fn($v) => $v['id'] != $product->id);
        array_unshift($viewedProducts, [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'image_url' => $product->image ? url($product->image) : null,
            'viewed_at' => now()
        ]);
        session()->put('viewed_products', array_slice($viewedProducts, 0, 10));
    }

    public function compare(Request $request)
    {
        $productIds = $request->get('products', []);
        if (empty($productIds)) {
            return redirect()->route('products.index')->with('error', 'Select products to compare.');
        }

        $products = Product::whereIn('id', $productIds)->where('is_active', true)->get();
        if ($products->count() < 2) {
            return redirect()->route('products.index')->with('error', 'Select at least 2 products.');
        }

        return view('products.compare', compact('products'));
    }

    // API: Get all products
    public function apiIndex()
    {
        try {
            $products = Product::where('is_active', true)
                ->with(['category', 'brand'])
                ->latest()
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'discount_price' => $product->discount_price,
                        'description' => $product->description,
                        'sku' => $product->sku,
                        'stock_quantity' => $product->stock_quantity,
                        'category_id' => $product->category_id,
                        'brand_id' => $product->brand_id,
                        'brand_name' => $product->brand ? $product->brand->name : null,
                        'image_url' => $product->image ? url($product->image) : null,
                        'category' => $product->category ? ['id' => $product->category->id, 'name' => $product->category->name] : null,
                        'specifications' => is_string($product->specifications) ? json_decode($product->specifications, true) : $product->specifications,
                        'is_active' => $product->is_active,
                        'is_featured' => $product->is_featured,
                    ];
                });

            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch products', 'error' => $e->getMessage()], 500);
        }
    }

    // API: Get single product by ID
    public function apiShow($id)
    {
        try {
            $product = Product::where('is_active', true)
                ->with(['category', 'brand', 'reviews.user'])
                ->findOrFail($id);

            return response()->json([
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'discount_price' => $product->discount_price,
                'description' => $product->description,
                'sku' => $product->sku,
                'stock_quantity' => $product->stock_quantity,
                'category_id' => $product->category_id,
                'brand_id' => $product->brand_id,
                'brand_name' => $product->brand ? $product->brand->name : null,
                'image_url' => $product->image ? url($product->image) : null,
                'category' => $product->category ? ['id' => $product->category->id, 'name' => $product->category->name] : null,
                'specifications' => is_string($product->specifications) ? json_decode($product->specifications, true) : $product->specifications,
                'is_active' => $product->is_active,
                'is_featured' => $product->is_featured,
                'reviews' => $product->reviews->map(fn($r) => [
                    'id' => $r->id,
                    'user' => $r->user ? $r->user->name : 'Anonymous',
                    'rating' => $r->rating,
                    'comment' => $r->comment,
                    'created_at' => $r->created_at->toDateTimeString(),
                ]),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Product not found', 'error' => $e->getMessage()], 404);
        }
    }
}
