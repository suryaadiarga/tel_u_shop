<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Exception;

class OrderController extends Controller
{
    /**
     * List order milik merchant (produk yang dijual merchant).
     */
    public function index(Request $request)
    {
        try {
            $merchantId = $request->user()->id;

            $orders = Order::with(['items.product', 'user'])
                ->whereHas('items.product', function ($query) use ($merchantId) {
                    $query->where('user_id', $merchantId);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $orders
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data pesanan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update status order (merchant).
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $merchantId = $request->user()->id;

            $request->validate([
                'status' => 'required|in:pending,processing,paid,shipped,completed,cancelled'
            ]);

            // Verify merchant ownership - check if merchant has products in this order
            $order = Order::findOrFail($id);

            $hasAccess = $order->items()
                ->whereHas('product', function ($query) use ($merchantId) {
                    $query->where('user_id', $merchantId);
                })
                ->exists();

            if (!$hasAccess) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke order ini.'
                ], 403);
            }

            $order->update([
                'status' => $request->input('status')
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Status order berhasil diperbarui.',
                'data' => $order
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui status order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
