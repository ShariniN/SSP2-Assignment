<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        // Create 8 carts
        Cart::factory()
            ->count(8)
            ->create()
            ->each(function ($cart) {
                // Add 2–4 cart items
                $items = CartItem::factory()
                    ->count(rand(2, 4))
                    ->create([
                        'cart_id' => $cart->id,
                        'product_id' => Product::inRandomOrder()->first()->id,
                    ]);

                // Update cart total from items
                $total = 0;
                foreach ($items as $item) {
                    $total += $item->product->price * $item->quantity;
                }
                $cart->update(['total' => $total]);
            });
    }
}
