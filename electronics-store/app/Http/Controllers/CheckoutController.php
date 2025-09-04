<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        return view('checkout', ['cartItems' => $cart, 'total' => $total]);
    }

    public function process(Request $request)
    {
        // In real app, save order to DB
        session()->forget('cart');
        return redirect()->route('home')->with('success', 'Order placed successfully!');
    }
}
