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
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock_quantity < $request->quantity) {
            return response()->json([
                'message' => 'Not enough stock available.'
            ], 400);
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            
            if ($newQuantity > $product->stock_quantity) {
                return response()->json([
                    'message' => 'Cannot add more items. Stock limit reached.'
                ], 400);
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        $cartItem->load('product');

        return response()->json($cartItem, 201);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::findOrFail($id);

        if ($cartItem->cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($cartItem->product->stock_quantity < $request->quantity) {
            return response()->json([
                'message' => 'Not enough stock available.'
            ], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);
        $cartItem->load('product');

        return response()->json($cartItem);
    }

    /**
     * Remove single item from cart
     */
    public function destroy($id)
    {
        $cartItem = CartItem::findOrFail($id);

        if ($cartItem->cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cartItem->delete();

        return response()->json([
            'message' => 'Item removed from cart successfully!'
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json([
            'message' => 'Cart cleared successfully!'
        ]);
    }
}