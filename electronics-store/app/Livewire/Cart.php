<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;

class Cart extends Component
{
    public $cart = [];

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function add($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return;
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
            ];
        }

        session()->put('cart', $cart);
        $this->cart = $cart;

        $this->dispatchBrowserEvent('cart-updated');
    }

    public function remove($productId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
            $this->cart = $cart;
        }
    }

    public function clear()
    {
        session()->forget('cart');
        $this->cart = [];
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
