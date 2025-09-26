<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories
     */
    public function index()
    {
        // Get categories with product count, only active categories
        $categories = Category::where('is_active', true)
                             ->withCount('products')
                             ->orderBy('name')
                             ->get();
        
        return view('categories.index', compact('categories'));
    }
    
    /**
     * Show products for a specific category
     */
    public function showProducts($id)
    {
        $category = Category::where('is_active', true)
                           ->findOrFail($id);
        
        // Get products for this category with pagination
        $products = Product::where('category_id', $id)
                          ->where('is_active', true)
                          ->with('category')
                          ->paginate(12);
        
        return view('categories.products', compact('category', 'products'));
    }
    
    /**
     * Search categories and products
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return redirect()->route('categories.index');
        }
        
        // Search in categories
        $categories = Category::where('is_active', true)
                             ->where(function($q) use ($query) {
                                 $q->where('name', 'LIKE', "%{$query}%")
                                   ->orWhere('description', 'LIKE', "%{$query}%");
                             })
                             ->withCount('products')
                             ->get();
        
        // Search in products
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
    
    /**
     * Get category by slug (if you're using slugs)
     */
    public function showBySlug($slug)
    {
        $category = Category::where('slug', $slug)
                           ->where('is_active', true)
                           ->firstOrFail();
        
        $products = Product::where('category_id', $category->id)
                          ->where('is_active', true)
                          ->with('category')
                          ->paginate(12);
        
        return view('categories.products', compact('category', 'products'));
    }
}