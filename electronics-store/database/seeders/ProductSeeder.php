<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $brands = Brand::all();

        foreach(range(1, 20) as $i) {
            Product::create([
                'name' => 'Product '.$i,
                'description' => 'Description for product '.$i,
                'price' => rand(50, 1000),
                'stock_quantity' => rand(10, 100),
                'SKU' => 'SKU'.rand(1000,9999),
                'category_id' => $categories->random()->id,
                'brand_id' => $brands->random()->id,
                'rating' => rand(1,5),
                'image_url' => 'https://via.placeholder.com/150'
            ]);
        }
    }
}

