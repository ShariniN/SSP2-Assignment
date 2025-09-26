<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Product;

class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        return [
            'cart_id' => Cart::factory(),      
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 3),
        ];
    }
}
