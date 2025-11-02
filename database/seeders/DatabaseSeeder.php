<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;
use App\Models\WalletTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'suryaadi@studenttelkomuniversity.ac.id'],
            [
                'name' => 'Surya Adi',
                'password' => Hash::make('password'),
                'nim' => '1203230101',
                'kelas' => 'IF-03-03',
                'phone' => '082231258553',
                'avatar_url' => '/images/surya.jpg',
                'ewallet_balance' => 45000,
            ]
        );

        $prods = [
            ['Kopi susu', 18000, '/images/kopi1.jpg', 3.4, 4331, 'Coffee'],
            ['Risol', 10000, '/images/risol.jpg', 4.9, 442, 'Snack'],
            ['Sosis', 12000, '/images/sosis.jpg', 4.1, 1040, 'Snack'],
            ['Air Mineral', 3000, '/images/air.jpg', 4.8, 290, 'Drink'],
            ['Dim sum', 15000, '/images/dimsum.jpg', 4.5, 120, 'Snack'],
        ];
        foreach ($prods as $p) {
            Product::updateOrCreate(['name' => $p[0]], [
                'price' => $p[1],
                'image_url' => $p[2],
                'rating' => $p[3],
                'reviews_count' => $p[4],
                'category' => $p[5],
            ]);
        }

        // transaksi ewallet (negatif = pembayaran)
        foreach ([
            ['Qris pembayaran Indomaret', -10000],
            ['Qris pembayaran Gebrek pedas', -15000],
            ['Qris pembayaran Indomaret', -5000],
            ['Topup', +20000],
        ] as $t) {
            WalletTransaction::create(['user_id' => $user->id, 'title' => $t[0], 'amount' => $t[1]]);
        }

        // activity / orders
        $o1 = Order::create(['status' => 'Selesai', 'total' => 17000, 'placed_at' => Carbon::parse('2025-03-12 09:25')]);
        OrderItem::create(['order_id' => $o1->id, 'product_name' => 'Cilok, Risol', 'qty' => 2, 'price' => 7000, 'thumb' => '/images/cilok.jpg']);

        $o2 = Order::create(['status' => 'Batal', 'total' => 10000, 'placed_at' => Carbon::parse('2025-03-12 18:17')]);
        OrderItem::create(['order_id' => $o2->id, 'product_name' => 'Risol', 'qty' => 1, 'price' => 10000, 'thumb' => '/images/risol.jpg']);

        $o3 = Order::create(['status' => 'Selesai', 'total' => 3000, 'placed_at' => Carbon::parse('2025-03-10 14:09')]);
        OrderItem::create(['order_id' => $o3->id, 'product_name' => 'Air Mineral', 'qty' => 1, 'price' => 3000, 'thumb' => '/images/air.jpg']);

        $o4 = Order::create(['status' => 'Selesai', 'total' => 15000, 'placed_at' => Carbon::parse('2025-03-08 14:09')]);
        OrderItem::create(['order_id' => $o4->id, 'product_name' => 'Dim sum', 'qty' => 1, 'price' => 15000, 'thumb' => '/images/dimsum.jpg']);

        $o5 = Order::create(['status' => 'Selesai', 'total' => 6000, 'placed_at' => Carbon::parse('2025-03-07 14:09')]);
        OrderItem::create(['order_id' => $o5->id, 'product_name' => 'Kurma', 'qty' => 1, 'price' => 6000, 'thumb' => '/images/kurma.jpg']);
    }
}
