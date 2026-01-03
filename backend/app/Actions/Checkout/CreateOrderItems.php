<?php

namespace App\Actions\Checkout;

use App\Models\Order;
use Illuminate\Support\Collection;

class CreateOrderItems
{
    /**
     * @param Collection<int, \App\Models\CartItem> $cartItems
     */
    public function execute(Order $order, Collection $cartItems): void
    {
        foreach ($cartItems as $cartItem) {
            $unitPrice = $cartItem->price_snapshot;
            $subtotal = $cartItem->qty * $unitPrice;

            $order->items()->create([
                'product_id' => $cartItem->product_id,
                'qty' => $cartItem->qty,
                'price' => $unitPrice,
                'subtotal' => $subtotal,
            ]);

            $cartItem->product->decrement('stock', $cartItem->qty);
        }
    }
}
