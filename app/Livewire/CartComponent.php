<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartComponent extends Component
{
    public $cartItems;
    public $subtotal = 0;
    public $shipping = 0;
    public $tax = 0;
    public $total = 0;
    public $quantity = [];
    
    // Add to cart specific properties
    public $product = null;
    public $addToCartQuantity = 1;
    public $showAddToCart = false;
    public $relatedProducts = null;
    public $showRelatedProducts = false;

    public function mount($product = null, $relatedProducts = null)
    {
        if ($product) {
            $this->product = $product;
            $this->showAddToCart = true;
        }
        
        if ($relatedProducts) {
            $this->relatedProducts = $relatedProducts;
            $this->showRelatedProducts = true;
        }
        
        $this->loadCart();
    }

    public function loadCart()
    {
        if (!Auth::check()) {
            $this->cartItems = collect();
            $this->calculateTotals();
            return;
        }

        $cart = Cart::where('user_id', Auth::id())->first();

        if ($cart) {
            $this->cartItems = $cart->items()->with(['product', 'product.category'])->get();
            
            // Initialize quantity array for cart management
            foreach ($this->cartItems as $item) {
                $this->quantity[$item->id] = $item->quantity;
            }
        } else {
            $this->cartItems = collect();
        }

        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $this->shipping = $this->subtotal >= 100 ? 0 : 9.99;
        $this->tax = $this->subtotal * 0.085;
        $this->total = $this->subtotal + $this->shipping + $this->tax;
    }

    // Add to cart functionality
    public function increaseAddQuantity()
    {
        if ($this->product && $this->addToCartQuantity < $this->product->stock_quantity) {
            $this->addToCartQuantity++;
        }
    }

    public function decreaseAddQuantity()
    {
        if ($this->addToCartQuantity > 1) {
            $this->addToCartQuantity--;
        }
    }

    public function addToCart()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to add items to cart.');
            return;
        }

        if (!$this->product || $this->product->stock_quantity < $this->addToCartQuantity) {
            session()->flash('error', 'Not enough stock available.');
            return;
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $this->product->id)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $this->addToCartQuantity;
            if ($newQuantity > $this->product->stock_quantity) {
                session()->flash('error', 'Cannot add more items. Stock limit reached.');
                return;
            }
            $existingItem->update(['quantity' => $newQuantity]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $this->product->id,
                'quantity' => $this->addToCartQuantity,
            ]);
        }

        $this->addToCartQuantity = 1;
        $this->loadCart();
        session()->flash('success', 'Product added to cart successfully!');
        $this->dispatch('cart-updated');
    }

    public function addRelatedToCart($productId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to add items to cart.');
            return;
        }

        $relatedProduct = Product::find($productId);
        if (!$relatedProduct || $relatedProduct->stock_quantity < 1) {
            session()->flash('error', 'Product not available.');
            return;
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + 1;
            if ($newQuantity > $relatedProduct->stock_quantity) {
                session()->flash('error', 'Cannot add more items. Stock limit reached.');
                return;
            }
            $existingItem->update(['quantity' => $newQuantity]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => 1,
            ]);
        }

        $this->loadCart();
        session()->flash('success', 'Product added to cart successfully!');
        $this->dispatch('cart-updated');
    }

    // Cart management functionality
    public function increaseQuantity($itemId)
    {
        if (!Auth::check()) return;

        $item = CartItem::find($itemId);
        if ($item && $item->cart->user_id === Auth::id()) {
            if ($item->quantity < $item->product->stock_quantity) {
                $item->increment('quantity');
                $this->quantity[$itemId] = $item->quantity;
                $this->loadCart();
                session()->flash('success', 'Item quantity updated!');
            } else {
                session()->flash('error', 'Cannot add more. Stock limit reached.');
            }
        }
    }

    public function decreaseQuantity($itemId)
    {
        if (!Auth::check()) return;

        $item = CartItem::find($itemId);
        if ($item && $item->cart->user_id === Auth::id() && $item->quantity > 1) {
            $item->decrement('quantity');
            $this->quantity[$itemId] = $item->quantity;
            $this->loadCart();
            session()->flash('success', 'Item quantity updated!');
        }
    }

    public function removeItem($itemId)
    {
        if (!Auth::check()) return;

        $item = CartItem::find($itemId);
        if ($item && $item->cart->user_id === Auth::id()) {
            unset($this->quantity[$itemId]);
            $item->delete();
            $this->loadCart();
            session()->flash('success', 'Item removed from cart!');
            $this->dispatch('cart-updated');
        }
    }

    public function clearCart()
    {
        if (!Auth::check()) return;

        $cart = Cart::where('user_id', Auth::id())->first();
        if ($cart) {
            $cart->items()->delete();
            $this->quantity = [];
            $this->loadCart();
            session()->flash('success', 'Cart cleared!');
            $this->dispatch('cart-updated');
        }
    }

    public function render()
    {
        return view('livewire.cart-component');
    }
}