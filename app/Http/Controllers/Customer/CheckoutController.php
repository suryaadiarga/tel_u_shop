<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class CheckoutController extends Controller
{
    /**
     * Memproses checkout dari keranjang belanja ke pesanan.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        try {
            DB::beginTransaction();

            // 1. Ambil cart dan items
            $cart = Cart::where('user_id', $user->id)->first();

            if (!$cart) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Keranjang belanja Anda kosong.'
                ], 400);
            }

            $cartItems = CartItem::with('product')
                ->where('cart_id', $cart->id)
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Keranjang belanja Anda kosong.'
                ], 400);
            }

            // 2. Hitung total harga dan validasi stok
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                if ($item->product->stock < $item->qty) {
                    throw new Exception("Stok produk '{$item->product->name}' tidak mencukupi.");
                }
                $totalAmount += $item->price_snapshot * $item->qty;
            }

            // 3. Validasi Saldo Wallet (gunakan wallet_balance di User model)
            if ($user->wallet_balance < $totalAmount) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Saldo dompet tidak mencukupi. Silakan top up terlebih dahulu.'
                ], 400);
            }

            // 4. Buat Order Utama
            $order = Order::create([
                'user_id' => $user->id,
                'total' => $totalAmount,
                'status' => 'pending',
                'placed_at' => now(),
            ]);

            // 5. Pindahkan item keranjang ke OrderItems & Update Stok
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'qty' => $item->qty,
                    'price' => $item->price_snapshot,
                    'subtotal' => $item->qty * $item->price_snapshot,
                ]);

                // Kurangi stok produk
                $item->product->decrement('stock', $item->qty);
            }

            // 6. Potong Saldo Wallet
            $user->decrement('wallet_balance', $totalAmount);

            // 7. Catat transaksi wallet
            WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => -$totalAmount,
                'title' => 'Pembayaran Order #' . $order->id,
                'type' => 'payment',
                'order_id' => $order->id,
                'description' => 'Pembayaran order ke merchant',
            ]);

            // 8. Kosongkan Keranjang
            CartItem::where('cart_id', $cart->id)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Checkout berhasil dilakukan.',
                'data' => [
                    'order_id' => $order->id,
                    'total_paid' => $totalAmount,
                    'order' => $order->load('items.product'),
                ]
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat checkout.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
