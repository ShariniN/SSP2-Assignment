<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->word();

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),                // auto-generate slug
            'description' => $this->faker->sentence(), // short description
            'is_active' => $this->faker->boolean(90),  // 90% active
        ];
    }
}
