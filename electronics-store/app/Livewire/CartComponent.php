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
    
    // Add to cart properties
    public $quantity = [];
    public $showAddToCart = false;
    public $product = null;
    public $addToCartQuantity = 1;

    protected $listeners = ['productSelected' => 'setProduct'];

    public function mount($product = null)
    {
        if ($product) {
            $this->product = $product;
            $this->showAddToCart = true;
            $this->addToCartQuantity = 1;
        }
        $this->loadCart();
    }

    public function setProduct($productId)
    {
        $this->product = Product::find($productId);
        $this->showAddToCart = true;
        $this->addToCartQuantity = 1;
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
            
            // Initialize quantity array for existing items
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

        // Calculate shipping (free shipping over $100)
        $this->shipping = $this->subtotal >= 100 ? 0 : 9.99;

        // Calculate tax (8.5% tax rate)
        $this->tax = $this->subtotal * 0.085;

        // Calculate total
        $this->total = $this->subtotal + $this->shipping + $this->tax;
    }

    // Add to Cart Methods
    public function increaseAddToCartQuantity()
    {
        if ($this->product && $this->addToCartQuantity < $this->product->stock_quantity) {
            $this->addToCartQuantity++;
        }
    }

    public function decreaseAddToCartQuantity()
    {
        if ($this->addToCartQuantity > 1) {
            $this->addToCartQuantity--;
        }
    }

    public function addToCart($productId = null, $qty = null)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to add items to cart.');
            return redirect()->route('login');
        }

        // Determine product and quantity
        $product = $productId ? Product::find($productId) : $this->product;
        $quantity = $qty ?? $this->addToCartQuantity;

        if (!$product) {
            session()->flash('error', 'Product not found.');
            return;
        }

        if ($product->stock_quantity < $quantity) {
            session()->flash('error', 'Not enough stock available.');
            return;
        }

        // Get or create cart
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        // Check if item already exists
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            if ($newQuantity > $product->stock_quantity) {
                session()->flash('error', 'Cannot add more items. Stock limit reached.');
                return;
            }
            $existingItem->quantity = $newQuantity;
            $existingItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        $this->loadCart();
        session()->flash('success', 'Product added to cart successfully!');
        
        // Reset quantity for the add to cart form
        $this->addToCartQuantity = 1;
        
        // Dispatch event for other components
        $this->dispatch('cart-updated');
    }

    // Existing Cart Management Methods
    public function increaseQuantity($itemId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to manage your cart.');
            return;
        }

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
        if (!Auth::check()) {
            session()->flash('error', 'Please login to manage your cart.');
            return;
        }

        $item = CartItem::find($itemId);
        if ($item && $item->cart->user_id === Auth::id() && $item->quantity > 1) {
            $item->decrement('quantity');
            $this->quantity[$itemId] = $item->quantity;
            $this->loadCart();
            session()->flash('success', 'Item quantity updated!');
        }
    }

    public function updateQuantity($itemId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to manage your cart.');
            return;
        }

        $item = CartItem::find($itemId);
        if ($item && $item->cart->user_id === Auth::id()) {
            $newQuantity = $this->quantity[$itemId];
            
            if ($newQuantity < 1) {
                $newQuantity = 1;
            } elseif ($newQuantity > $item->product->stock_quantity) {
                $newQuantity = $item->product->stock_quantity;
                session()->flash('error', 'Quantity adjusted to available stock.');
            }
            
            $item->quantity = $newQuantity;
            $item->save();
            $this->quantity[$itemId] = $newQuantity;
            $this->loadCart();
            session()->flash('success', 'Item quantity updated!');
        }
    }

    public function removeItem($itemId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to manage your cart.');
            return;
        }

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
        if (!Auth::check()) {
            session()->flash('error', 'Please login to manage your cart.');
            return;
        }

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