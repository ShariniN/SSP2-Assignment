<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 1000, 200000), 
            'stock_quantity' => $this->faker->numberBetween(5, 100),
            'SKU' => strtoupper(Str::random(8)),
            'category_id' => Category::factory(), 
            'brand_id' => Brand::factory(),       
            'rating' => $this->faker->randomFloat(2, 1, 5),
            'image_url' => $this->faker->imageUrl(640, 480, 'electronics', true),
            'discount_price' => $this->faker->randomFloat(2, 500, 150000),
            'is_featured' => $this->faker->boolean(20), 
        ];
    }
}
