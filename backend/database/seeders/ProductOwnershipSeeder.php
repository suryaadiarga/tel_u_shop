<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductOwnershipSeeder extends Seeder
{
    public function run(): void
    {
        $merchant = User::where('email', 'merchant@koperasi.test')->first();

        if (!$merchant) {
            return;
        }

        Product::whereNull('merchant_id')->update(['merchant_id' => $merchant->id]);
        Product::whereNull('user_id')->update(['user_id' => $merchant->id]);
    }
}
