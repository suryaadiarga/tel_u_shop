<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Generate 20 produk dummy
        Product::factory()->count(20)->create();
    }
}