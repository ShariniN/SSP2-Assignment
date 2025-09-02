<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Categories
        Category::insert([
            ['name' => 'Phones'],
            ['name' => 'Laptops'],
            ['name' => 'Audio'],
            ['name' => 'Wearables'],
            ['name' => 'Accessories'],
        ]);

        // Seed Products
        Product::insert([
            [
                'name' => 'iPhone 15',
                'description' => 'Latest iPhone',
                'price' => 1200,
                'stock' => 10,
                'category_id' => 1
            ],
            [
                'name' => 'MacBook Pro',
                'description' => 'M2 Chip',
                'price' => 2500,
                'stock' => 5,
                'category_id' => 2
            ],
        ]);
    }
}
