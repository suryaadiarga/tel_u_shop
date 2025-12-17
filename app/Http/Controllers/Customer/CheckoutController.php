<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Process checkout
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:wallet,cash',
            'notes' => 'nullable|string|max:500'
        ]);

        $user = $request->user();

        try {
            DB::beginTransaction();

            // Get user's cart
            $cart = $user->cart;
            if (!$cart || $cart->items->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cart is empty'
                ], 422);
            }

            $totalAmount = $cart->total();

            // Check wallet balance if paying with wallet
            if ($request->payment_method === 'wallet') {
                if ($user->wallet_balance < $totalAmount) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Insufficient wallet balance'
                    ], 422);
                }
            }

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'placed_at' => now(),
            ]);

            // Create order items
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->qty,
                    'price_snapshot' => $cartItem->price_snapshot,
                ]);

                // Decrease product stock
                $cartItem->product->decrement('stock', $cartItem->qty);
            }

            // Deduct from wallet if paying with wallet
            if ($request->payment_method === 'wallet') {
                $user->decrement('wallet_balance', $totalAmount);

                // Record wallet transaction
                $user->walletTransactions()->create([
                    'type' => 'payment',
                    'amount' => -$totalAmount,
                    'description' => 'Payment for order #' . $order->id,
                    'status' => 'completed'
                ]);
            }

            // Award loyalty points (1 point per 1000 spent)
            $pointsEarned = floor($totalAmount / 1000);
            if ($pointsEarned > 0) {
                LoyaltyPoint::create([
                    'user_id' => $user->id,
                    'points' => $pointsEarned,
                    'type' => 'earned',
                    'description' => 'Points earned from order #' . $order->id,
                ]);

                $user->increment('loyalty_points', $pointsEarned);
            }

            // Clear cart
            $cart->items()->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order placed successfully',
                'data' => [
                    'order' => $order->load('items.product'),
                    'points_earned' => $pointsEarned ?? 0
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process checkout',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
