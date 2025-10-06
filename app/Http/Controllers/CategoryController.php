<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::where('is_active', true)
                              ->withCount('products')
                              ->orderBy('name')
                              ->get();
        return view('categories.index', compact('categories'));
    }

    public function showProducts($id)
    {
        $category = Category::where('is_active', true)->findOrFail($id);

        $products = Product::where('category_id', $id)
                           ->where('is_active', true)
                           ->with('category')
                           ->paginate(12);

        return view('category', compact('category', 'products'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        if (empty($query)) {
            return redirect()->route('categories.index');
        }

        $categories = Category::where('is_active', true)
                              ->where(function($q) use ($query) {
                                  $q->where('name', 'LIKE', "%{$query}%")
                                    ->orWhere('description', 'LIKE', "%{$query}%");
                              })
                              ->withCount('products')
                              ->get();

        $products = Product::where('is_active', true)
                           ->where(function($q) use ($query) {
                               $q->where('name', 'LIKE', "%{$query}%")
                                 ->orWhere('description', 'LIKE', "%{$query}%")
                                 ->orWhere('brand', 'LIKE', "%{$query}%");
                           })
                           ->with('category')
                           ->paginate(12);

        return view('categories.search', compact('categories', 'products', 'query'));
    }

    public function showBySlug($slug)
    {
        $category = Category::where('slug', $slug)
                            ->where('is_active', true)
                            ->firstOrFail();

        $products = Product::where('category_id', $category->id)
                           ->where('is_active', true)
                           ->with('category')
                           ->paginate(12);

        return view('category', compact('category', 'products'));
    }

    public function apiIndex()
    {
        try {
            $categories = Category::where('is_active', true)
                                  ->withCount('products')
                                  ->orderBy('name')
                                  ->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // In CategoryController.php - apiProducts method
public function apiProducts($id)
{
    try {
        $category = Category::where('is_active', true)->findOrFail($id);
        
        $products = $category->products()
            ->where('is_active', true)
            ->with('brand') // Add this
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
                    'brand_id' => $product->brand_id, // Add this
                    'brand_name' => $product->brand ? $product->brand->name : null, // Add this
                    'image_url' => $product->image ? asset('storage/' . $product->image) : null,
                    'specifications' => is_string($product->specifications)
                        ? json_decode($product->specifications, true)
                        : $product->specifications,
                    'is_active' => $product->is_active,
                    'is_featured' => $product->is_featured,
                ];
            });

        return response()->json(['products' => $products], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch products',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function apiSearch(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Search query cannot be empty'
            ], 400);
        }

        try {
            $categories = Category::where('is_active', true)
                                  ->where(function ($q) use ($query) {
                                      $q->where('name', 'LIKE', "%{$query}%")
                                        ->orWhere('description', 'LIKE', "%{$query}%");
                                  })
                                  ->withCount('products')
                                  ->get();

            $products = Product::where('is_active', true)
                               ->where(function ($q) use ($query) {
                                   $q->where('name', 'LIKE', "%{$query}%")
                                     ->orWhere('description', 'LIKE', "%{$query}%")
                                     ->orWhere('brand', 'LIKE', "%{$query}%");
                               })
                               ->with('category')
                               ->get();

            return response()->json([
                'success' => true,
                'categories' => $categories,
                'products' => $products
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function apiShowBySlug($slug)
    {
        try {
            $category = Category::where('slug', $slug)
                                ->where('is_active', true)
                                ->firstOrFail();

            $products = Product::where('category_id', $category->id)
                               ->where('is_active', true)
                               ->with('category')
                               ->get();

            return response()->json([
                'success' => true,
                'category' => $category,
                'products' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch category by slug',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
