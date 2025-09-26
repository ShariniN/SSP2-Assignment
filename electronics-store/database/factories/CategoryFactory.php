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
    'name' => ucfirst($this->faker->unique()->word()),
    'description' => $this->faker->sentence(),
    'is_active' => $this->faker->boolean(90),
    ];

    }
}
