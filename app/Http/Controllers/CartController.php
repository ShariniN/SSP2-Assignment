<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Get user's cart with items
     */
    public function index()
    {
        $cart = Cart::where('user_id', Auth::id())
            ->with(['items.product'])
            ->first();

        if (!$cart) {
            return response()->json([]);
        }

        return response()->json($cart->items);
    }

    /**
     * Add item to cart
     */

    public function store(Request $request)
{
    \Log::info('=== CART STORE START ===');
    \Log::info('User ID: ' . Auth::id());
    \Log::info('Request data:', $request->all());

    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
    ]);

    $product = Product::findOrFail($request->product_id);
    \Log::info('Product found:', ['id' => $product->id, 'name' => $product->name]);

    if ($product->stock_quantity < $request->quantity) {
        return response()->json([
            'message' => 'Not enough stock available.'
        ], 400);
    }

    // Check if cart exists
    $existingCart = Cart::where('user_id', Auth::id())->first();
    \Log::info('Existing cart:', ['exists' => $existingCart ? 'yes' : 'no']);

    $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
    \Log::info('Cart after firstOrCreate:', ['id' => $cart->id, 'user_id' => $cart->user_id]);

    // Check if cart item exists
    $existingCartItem = CartItem::where('cart_id', $cart->id)
        ->where('product_id', $request->product_id)
        ->first();
    \Log::info('Existing cart item:', ['exists' => $existingCartItem ? 'yes' : 'no']);

    $cartItem = CartItem::where('cart_id', $cart->id)
        ->where('product_id', $request->product_id)
        ->first();

    if ($cartItem) {
        \Log::info('Updating existing cart item');
        $newQuantity = $cartItem->quantity + $request->quantity;
        
        if ($newQuantity > $product->stock_quantity) {
            return response()->json([
                'message' => 'Cannot add more items. Stock limit reached.'
            ], 400);
        }

        $cartItem->update(['quantity' => $newQuantity]);
        \Log::info('Cart item updated:', ['id' => $cartItem->id, 'new_quantity' => $newQuantity]);
    } else {
        \Log::info('Creating new cart item');
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);
        \Log::info('Cart item created:', ['id' => $cartItem->id]);
    }

    // Verify the data was saved
    $verifyCart = Cart::find($cart->id);
    $verifyCartItem = CartItem::find($cartItem->id);
    \Log::info('Verification - Cart:', ['exists' => $verifyCart ? 'yes' : 'no']);
    \Log::info('Verification - CartItem:', ['exists' => $verifyCartItem ? 'yes' : 'no']);

    $cartItem->load('product');
    \Log::info('=== CART STORE END ===');

    return response()->json($cartItem, 201);
}
}