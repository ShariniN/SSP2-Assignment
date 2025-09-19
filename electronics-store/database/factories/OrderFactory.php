<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\User;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // link to a user
            'status' => $this->faker->randomElement(['pending', 'shipped', 'delivered']),
            'total_amount' => $this->faker->randomFloat(2, 2000, 50000),
            'shipping_address' => $this->faker->address(),
        ];
    }
}
