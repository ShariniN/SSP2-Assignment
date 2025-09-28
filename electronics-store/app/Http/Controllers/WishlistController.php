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

            // Get wishlist items from MongoDB
            $wishlistItems = Wishlist::where('user_id', Auth::id())->get();
            
            // Manually load products from MongoDB
            $items = collect();
            foreach ($wishlistItems as $wishlistItem) {
                try {
                    $product = Product::find($wishlistItem->product_id);
                    if ($product) {
                        // Add product data to the wishlist item
                        $wishlistItem->product = $product;
                        $items->push($wishlistItem);
                    }
                } catch (\Exception $e) {
                    Log::warning("Product not found for wishlist item: " . $wishlistItem->product_id);
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

            // Check if already exists
            $exists = Wishlist::where('user_id', Auth::id())
                             ->where('product_id', $productId)
                             ->exists();

            if (!$exists) {
                Wishlist::create([
                    'user_id' => Auth::id(),
                    'product_id' => $productId,
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

            Wishlist::where('user_id', Auth::id())
                    ->where('product_id', $productId)
                    ->delete();

            return back()->with('success', 'Product removed from wishlist!');

        } catch (\Exception $e) {
            Log::error('Wishlist remove error: ' . $e->getMessage());
            return back()->with('error', 'Unable to remove product from wishlist. Please try again.');
        }
    }
}