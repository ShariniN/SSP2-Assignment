<?php
namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Wishlist::where('user_id', Auth::id())->with('product')->get();
        return view('wishlist.index', compact('items'));
    }

    public function add($productId)
    {
        Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $productId,
        ]);

        return back()->with('success', 'Product added to wishlist!');
    }

    public function remove($productId)
    {
        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->delete();

        return back()->with('success', 'Product removed from wishlist!');
    }
}
