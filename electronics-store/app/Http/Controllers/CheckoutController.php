<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;

class CheckoutController extends Controller
{
    /**
     * Show checkout page
     */
    public function index()
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart || $cart->items()->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $cartItems = $cart->items()->with('product')->get()->filter(fn($item) => $item->product);

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Calculate totals
        $subtotal = $cartItems->sum(fn($item) => ($item->product->discount_price ?? $item->product->price) * $item->quantity);
        $shipping = $subtotal >= 100 ? 0 : 9.99;
        $tax = $subtotal * 0.085;
        $total = $subtotal + $shipping + $tax;

        return view('checkout.index', compact('cartItems', 'subtotal', 'shipping', 'tax', 'total'));
    }

    /**
     * Process checkout: create order, order items, clear cart
     */
    public function process(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'payment_method' => 'required|in:cod,card',
        ]);

        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart || $cart->items()->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $cartItems = $cart->items()->with('product')->get()->filter(fn($item) => $item->product);

        $subtotal = $cartItems->sum(fn($item) => ($item->product->discount_price ?? $item->product->price) * $item->quantity);
        $shipping = $subtotal >= 100 ? 0 : 9.99;
        $tax = $subtotal * 0.085;
        $total = $subtotal + $shipping + $tax;

        // Create Order
        $order = Order::create([
            'user_id' => Auth::id(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'country' => $request->country,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        // Create Order Items
        foreach ($cartItems as $item) {
            $order->items()->create([
                'product_id' => $item->product->id,
                'quantity' => $item->quantity,
                'price' => $item->product->discount_price ?? $item->product->price,
            ]);
        }

        // Clear cart
        $cart->items()->delete();

        return redirect()->route('checkout.success', ['order_id' => $order->id]);
    }

    /**
     * Show success page
     */
    public function success(Request $request)
    {
        $order = Order::with('items.product')->findOrFail($request->query('order_id'));
        return view('checkout.success', compact('order'));
    }
}
