<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Display the cart page
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $subtotal = 0;

        // Convert cart session data to display format
        foreach ($cart as $id => $details) {
            $product = Product::find($id);
            
            if ($product) {
                $itemTotal = $details['price'] * $details['quantity'];
                $cartItems[] = [
                    'id' => $id,
                    'name' => $details['name'],
                    'price' => $details['price'],
                    'quantity' => $details['quantity'],
                    'image' => $product->image ?? 'https://via.placeholder.com/80x80',
                    'description' => $product->description ?? 'Product description',
                    'variant' => $details['variant'] ?? null,
                    'total' => $itemTotal
                ];
                $subtotal += $itemTotal;
            }
        }

        // Calculate totals
        $shipping = $this->calculateShipping($subtotal);
        $tax = $this->calculateTax($subtotal);
        $total = $subtotal + $shipping + $tax;

        return view('cart', compact('cartItems', 'subtotal', 'shipping', 'tax', 'total'));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $quantity = $request->input('quantity', 1);
        $variant = $request->input('variant', null);

        $cart = session()->get('cart', []);
        
        // Create unique key for product with variant
        $cartKey = $variant ? $id . '_' . $variant : $id;
        
        if(isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                "name" => $product->name,
                "price" => $product->price,
                "quantity" => $quantity,
                "variant" => $variant
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
            
            return redirect()->route('cart')->with('success', 'Cart updated successfully!');
        }

        return redirect()->route('cart')->with('error', 'Item not found in cart!');
    }

    /**
     * Remove item from cart
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            
            return redirect()->route('cart')->with('success', 'Item removed from cart!');
        }

        return redirect()->route('cart')->with('error', 'Item not found in cart!');
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        session()->forget('cart');
        
        return redirect()->route('cart')->with('success', 'Cart cleared successfully!');
    }

    /**
     * Get cart count for navbar/header display
     */
    public function getCount()
    {
        $cart = session()->get('cart', []);
        $count = array_sum(array_column($cart, 'quantity'));
        
        return response()->json(['count' => $count]);
    }

    /**
     * Apply promo code
     */
    public function applyPromo(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string|max:50'
        ]);

        $promoCode = strtoupper($request->promo_code);
        $discount = $this->validatePromoCode($promoCode);

        if ($discount > 0) {
            session()->put('promo_code', $promoCode);
            session()->put('promo_discount', $discount);
            
            return redirect()->route('cart')->with('success', "Promo code applied! You saved {$discount}%");
        }

        return redirect()->route('cart')->with('error', 'Invalid or expired promo code!');
    }

    /**
     * Remove promo code
     */
    public function removePromo()
    {
        session()->forget(['promo_code', 'promo_discount']);
        
        return redirect()->route('cart')->with('success', 'Promo code removed!');
    }

    /**
     * Calculate shipping cost
     */
    private function calculateShipping($subtotal)
    {
        // Free shipping over $100
        if ($subtotal >= 100) {
            return 0;
        }

        // Flat rate shipping
        return 9.99;
    }

    /**
     * Calculate tax
     */
    private function calculateTax($subtotal)
    {
        // 8.5% tax rate
        $taxRate = 0.085;
        return $subtotal * $taxRate;
    }

    /**
     * Validate promo code and return discount percentage
     */
    private function validatePromoCode($code)
    {
        $promoCodes = [
            'SAVE10' => 10,
            'WELCOME20' => 20,
            'FREESHIP' => 5,
            'NEWUSER' => 15
        ];

        return $promoCodes[$code] ?? 0;
    }

    /**
     * Get cart totals (for AJAX requests)
     */
    public function getTotals()
    {
        $cart = session()->get('cart', []);
        $subtotal = 0;

        foreach ($cart as $id => $details) {
            $subtotal += $details['price'] * $details['quantity'];
        }

        $promoDiscount = session()->get('promo_discount', 0);
        $discountAmount = ($subtotal * $promoDiscount) / 100;
        $discountedSubtotal = $subtotal - $discountAmount;

        $shipping = $this->calculateShipping($discountedSubtotal);
        $tax = $this->calculateTax($discountedSubtotal);
        $total = $discountedSubtotal + $shipping + $tax;

        return response()->json([
            'subtotal' => number_format($subtotal, 2),
            'discount' => number_format($discountAmount, 2),
            'shipping' => number_format($shipping, 2),
            'tax' => number_format($tax, 2),
            'total' => number_format($total, 2),
            'items_count' => array_sum(array_column($cart, 'quantity'))
        ]);
    }

    /**
     * Update multiple cart items at once
     */
    public function updateMultiple(Request $request)
    {
        $cart = session()->get('cart', []);
        $quantities = $request->input('quantities', []);

        foreach ($quantities as $id => $quantity) {
            if (isset($cart[$id]) && $quantity > 0) {
                $cart[$id]['quantity'] = (int) $quantity;
            } elseif (isset($cart[$id]) && $quantity <= 0) {
                unset($cart[$id]);
            }
        }

        session()->put('cart', $cart);

        return redirect()->route('cart')->with('success', 'Cart updated successfully!');
    }
}