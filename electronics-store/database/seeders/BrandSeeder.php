<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        // Create 8 sample brands
        Brand::factory()->count(8)->create();
    }
}
