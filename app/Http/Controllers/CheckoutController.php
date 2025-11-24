<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;

class CheckoutController extends Controller
{
    public function store()
    {
        $user = auth()->user();
        $cart = $user->cart()->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return back()->withErrors(['cart' => 'Keranjang kosong']);
        }

        return \DB::transaction(function () use ($user, $cart) {
            $total = 0;
            foreach ($cart->items as $ci) {
                $total += $ci->qty * $ci->price_snapshot;
            }

            if ($user->ewallet_balance < $total) {
                return back()->withErrors(['balance' => 'Saldo tidak cukup'])->throwResponse();
            }

            $user->decrement('ewallet_balance', $total);
            $user->walletTransactions()->create([
                'title' => 'Pembayaran pesanan',
                'amount' => -$total,
            ]);

            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'Selesai',
                'total' => $total,
                'placed_at' => now(),
            ]);

            foreach ($cart->items as $ci) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_name' => $ci->product->name,
                    'qty' => $ci->qty,
                    'price' => $ci->price_snapshot,
                    'thumb' => $ci->product->image_url,
                ]);
            }

            $cart->items()->delete();

            return redirect()->route('activity')->with('toast', 'Pesanan berhasil dibayar.');
        });
    }
}
