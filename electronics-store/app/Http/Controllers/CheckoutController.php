<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = 10.00; // Fixed shipping cost
        $tax = $subtotal * 0.08; // 8% tax
        $total = $subtotal + $shipping + $tax;

        return view('checkout', [
            'cartItems' => $cart,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total
        ]);
    }

    public function process(Request $request)
    {
        // Validate checkout form
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'country' => 'required|string|max:255',
            'payment_method' => 'required|in:credit_card,debit_card,paypal',
            'card_number' => 'required_if:payment_method,credit_card,debit_card|string|max:19',
            'card_expiry' => 'required_if:payment_method,credit_card,debit_card|string|max:5',
            'card_cvv' => 'required_if:payment_method,credit_card,debit_card|string|max:4',
            'card_name' => 'required_if:payment_method,credit_card,debit_card|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Calculate totals
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = 10.00;
        $tax = $subtotal * 0.08;
        $total = $subtotal + $shipping + $tax;

        // In a real application, you would:
        // 1. Save the order to the database
        // 2. Process payment with payment gateway
        // 3. Send confirmation email
        // 4. Update inventory
        
        /*
        $order = Order::create([
            'user_id' => auth()->id(),
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
            'status' => 'pending'
        ]);

        foreach ($cart as $id => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity']
            ]);
        }
        */

        // Clear the cart
        session()->forget('cart');

        return redirect()->route('checkout.success')
            ->with('success', 'Order placed successfully! Order number: #' . rand(100000, 999999));
    }

    public function success()
    {
        return view('checkout-success');
    }
}