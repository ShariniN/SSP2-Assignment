<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Phones', 'Laptops', 'Wearables', 'Accessories', 'Audio'];
        foreach($categories as $cat) {
            Category::create(['name' => $cat, 'description' => $cat.' category']);
        }
    }
}

