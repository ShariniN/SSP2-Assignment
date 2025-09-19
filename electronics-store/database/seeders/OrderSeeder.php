<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Create 10 orders, each with 3–5 items
        Order::factory()
            ->count(10)
            ->create()
            ->each(function ($order) {
                OrderItem::factory()
                    ->count(rand(3, 5))
                    ->create([
                        'order_id' => $order->id,
                    ]);
            });
    }
}
