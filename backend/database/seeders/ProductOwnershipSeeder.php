<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductOwnershipSeeder extends Seeder
{
    public function run(): void
    {
        $preferredEmails = [
            'galang@merchant.test',
            'faruq@merchant.test',
        ];
        $merchants = User::whereIn('email', $preferredEmails)
            ->orderBy('id')
            ->get(['id']);

        if ($merchants->isEmpty()) {
            $merchants = User::where('role_id', 2)
                ->where('merchant_status', 'approved')
                ->where('is_banned', false)
                ->orderBy('id')
                ->get(['id']);
        }

        if ($merchants->isEmpty()) {
            $merchants = User::where('role_id', 2)->orderBy('id')->get(['id']);
        }

        if ($merchants->isEmpty()) {
            return;
        }

        $merchantIds = $merchants->pluck('id')->values();
        $merchantCount = $merchantIds->count();

        $offset = 0;
        Product::orderBy('id')
            ->chunkById(200, function ($products) use ($merchantIds, $merchantCount, &$offset) {
                $index = 0;
                foreach ($products as $product) {
                    $merchantId = $merchantIds[($offset + $index) % $merchantCount];
                    $product->update([
                        'merchant_id' => $merchantId,
                        'user_id' => $merchantId,
                    ]);
                    $index++;
                }
                $offset += $products->count();
            });
    }
}
