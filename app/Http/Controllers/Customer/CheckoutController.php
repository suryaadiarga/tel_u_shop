<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\WalletTransaction;

class CheckoutController extends Controller
{
    /**
     * Proses checkout keranjang user.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'error' => 'Keranjang kosong.'
            ], 400);
        }

        // Hitung total
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['qty']);

        // Cek saldo wallet
        if ($user->wallet_balance < $total) {
            return response()->json([
                'error' => 'Saldo wallet tidak cukup.'
            ], 402);
        }

        DB::beginTransaction();
        try {
            // Buat order
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'paid',
                'total_amount' => $total,
                'placed_at' => now(),
            ]);

            // Buat order items
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                ]);

                // Kurangi stok produk
                Product::where('id', $item['product_id'])
                    ->decrement('stock', $item['qty']);
            }

            // Catat transaksi wallet
            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'payment',
                'amount' => $total,
                'description' => 'Pembayaran order #' . $order->id,
            ]);

            // Update saldo wallet user
            $user->decrement('wallet_balance', $total);

            DB::commit();

            // Kosongkan cart
            session()->forget('cart');

            return response()->json([
                'message' => 'Checkout berhasil.',
                'order' => $order->load('items'),
                'wallet' => [
                    'balance' => $user->wallet_balance,
                    'last_transaction' => 'payment',
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Checkout gagal.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}