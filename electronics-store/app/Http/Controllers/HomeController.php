<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured products (latest 8 products)
        $featuredProducts = Product::where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        // Get products by categories for display
        $categories = Category::where('is_active', true)
            ->withCount(['products' => function($query) {
                $query->where('is_active', true);
            }])
            ->having('products_count', '>', 0)
            ->take(6)
            ->get();

        // Get best selling products (you can modify this based on your sales logic)
        $bestSellers = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('created_at', 'desc') // Replace with actual sales count when available
            ->take(4)
            ->get();

        // Get on sale products (products with discount)
        $saleProducts = Product::where('is_active', true)
            ->where('discount_price', '>', 0)
            ->orWhere('is_featured', true)
            ->take(4)
            ->get();

        // Get cart count for header
        $cartCount = collect(session()->get('cart', []))->sum('quantity');

        return view('home', compact(
            'featuredProducts',
            'categories',
            'bestSellers', 
            'saleProducts',
            'cartCount'
        ));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return redirect()->route('home');
        }

        $products = Product::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->paginate(12);

        return view('search-results', compact('products', 'query'));
    }

    public function category($id)
    {
    $category = Category::where('id', $id)
        ->where('is_active', true)
        ->firstOrFail();

    $products = Product::where('category_id', $category->id)
        ->where('is_active', true)
        ->paginate(12);

    return view('category', compact('category', 'products'));
    }

}