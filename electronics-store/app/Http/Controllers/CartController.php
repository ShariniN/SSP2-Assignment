<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Apply middleware to ensure only authenticated users can access cart
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the cart page
     */
    public function index()
    {
        $cartItems = $this->getCartItems();

        // Calculate totals
        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $shipping = $subtotal >= 100 ? 0 : 9.99;
        $tax = $subtotal * 0.085;
        $total = $subtotal + $shipping + $tax;

        return view('cart.index', compact('cartItems', 'subtotal', 'shipping', 'tax', 'total'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'integer|min:1|max:99',
        ]);

        $product = Product::findOrFail($productId);
        $quantity = $request->input('quantity', 1);

        // Check stock availability
        if (isset($product->stock_quantity) && $product->stock_quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available',
            ], 400);
        }

        $cart = $this->getOrCreateCart();

        // Check if item already exists in cart
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;

            if (isset($product->stock_quantity) && $product->stock_quantity < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available',
                ], 400);
            }

            $existingItem->update(['quantity' => $newQuantity]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item added to cart successfully',
            ]);
        }

        return redirect()->back()->with('success', 'Item added to cart successfully');
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $cartItem = $this->getCartItem($itemId);

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
            ], 404);
        }

        if (isset($cartItem->product->stock_quantity) && $cartItem->product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available',
            ], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove($itemId)
    {
        $cartItem = $this->getCartItem($itemId);

        if (!$cartItem) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found',
                ], 404);
            }

            return redirect()->back()->with('error', 'Cart item not found');
        }

        $cartItem->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
            ]);
        }

        return redirect()->back()->with('success', 'Item removed from cart');
    }

    /**
     * Clear the entire cart
     */
    public function clear()
    {
        $cart = $this->getCurrentCart();

        if ($cart) {
            $cart->items()->delete();
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
            ]);
        }

        return redirect()->back()->with('success', 'Cart cleared successfully');
    }

    /**
     * Get cart items count (for header display)
     */
    public function getCount()
    {
        $cartItems = $this->getCartItems();
        $count = $cartItems->sum('quantity');

        return response()->json(['count' => $count]);
    }

    /**
     * Get or create cart for authenticated user
     */
    private function getOrCreateCart()
    {
        return Cart::firstOrCreate([
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Get current cart
     */
    private function getCurrentCart()
    {
        return Cart::where('user_id', Auth::id())->first();
    }

    /**
     * Get all cart items for current user
     */
    private function getCartItems()
    {
        $cart = $this->getCurrentCart();

        if (!$cart) {
            return collect();
        }

        return CartItem::where('cart_id', $cart->id)
            ->with(['product', 'product.category'])
            ->get();
    }

    /**
     * Get specific cart item
     */
    private function getCartItem($itemId)
    {
        $cart = $this->getCurrentCart();

        if (!$cart) {
            return null;
        }

        return CartItem::where('id', $itemId)
            ->where('cart_id', $cart->id)
            ->with('product')
            ->first();
    }
}
