<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * List order milik merchant (produk yang dijual merchant).
     */
    public function index()
    {
        $orders = Order::with('items.product')
            ->whereHas('items.product', fn($q) => $q->where('merchant_id', auth()->id()))
            ->latest()
            ->get();

        return response()->json([
            'data' => $orders
        ]);
    }

    /**
     * Update status order (merchant).
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,paid,shipped,completed,cancelled'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Status order diperbarui oleh merchant.',
            'data' => $order
        ]);
    }
}