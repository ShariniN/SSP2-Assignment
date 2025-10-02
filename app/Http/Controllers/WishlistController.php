<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WishlistController extends Controller
{
    public function index()
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            // Get wishlist items from MongoDB - convert user_id to string
            $wishlistItems = Wishlist::where('user_id', (string)Auth::id())->get();
            
            // Manually load products from MySQL
            $items = collect();
            foreach ($wishlistItems as $wishlistItem) {
                try {
                    // Convert product_id string to integer for MySQL query
                    $productId = is_string($wishlistItem->product_id) 
                        ? (int)trim($wishlistItem->product_id, '"') 
                        : (int)$wishlistItem->product_id;
                    
                    $product = Product::find($productId);
                    
                    if ($product) {
                        // Add product data to the wishlist item
                        $wishlistItem->product = $product;
                        $items->push($wishlistItem);
                    } else {
                        Log::warning("Product not found for wishlist item. Product ID: " . $productId);
                    }
                } catch (\Exception $e) {
                    Log::warning("Error loading product for wishlist item: " . $e->getMessage());
                }
            }

            return view('wishlist.index', compact('items'));

        } catch (\Exception $e) {
            Log::error('Wishlist index error: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Unable to load wishlist. Please try again.');
        }
    }

    public function add($productId)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            // Store user_id and product_id as strings for consistency
            $userId = (string)Auth::id();
            $productIdString = (string)$productId;

            // Check if already exists
            $exists = Wishlist::where('user_id', $userId)
                             ->where(function($query) use ($productIdString) {
                                 $query->where('product_id', $productIdString)
                                       ->orWhere('product_id', '"' . $productIdString . '"');
                             })
                             ->exists();

            if (!$exists) {
                Wishlist::create([
                    'user_id' => $userId,
                    'product_id' => $productIdString, // Store as string without quotes
                ]);
            }

            return back()->with('success', 'Product added to wishlist!');

        } catch (\Exception $e) {
            Log::error('Wishlist add error: ' . $e->getMessage());
            return back()->with('error', 'Unable to add product to wishlist. Please try again.');
        }
    }

    public function remove($productId)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $userId = (string)Auth::id();
            $productIdString = (string)$productId;

            // Try multiple formats to ensure deletion
            Wishlist::where('user_id', $userId)
                    ->where(function($query) use ($productIdString) {
                        $query->where('product_id', $productIdString)
                              ->orWhere('product_id', '"' . $productIdString . '"')
                              ->orWhere('product_id', (int)$productIdString);
                    })
                    ->delete();

            return back()->with('success', 'Product removed from wishlist!');

        } catch (\Exception $e) {
            Log::error('Wishlist remove error: ' . $e->getMessage());
            return back()->with('error', 'Unable to remove product from wishlist. Please try again.');
        }
    }
}