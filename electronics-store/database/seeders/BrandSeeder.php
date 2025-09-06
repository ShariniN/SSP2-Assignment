<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = ['Samsung', 'Apple', 'Sony', 'Dell', 'HP'];
        foreach($brands as $brand) {
            Brand::create(['name' => $brand]);
        }
    }
}

