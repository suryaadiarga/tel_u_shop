<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Exception;

class OrderController extends Controller
{
    /**
     * List semua order (admin).
     */
    public function index(Request $request)
    {
        try {
            $query = Order::with(['items.product', 'user']);

            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $orders = $query->orderBy('created_at', 'desc')->get();

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
     * Detail order tertentu.
     */
    public function show($id)
    {
        try {
            $order = Order::with(['items.product', 'user'])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $order
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update status order (custom endpoint).
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            $request->validate([
                'status' => 'required|in:pending,paid,shipped,completed,cancelled'
            ]);

            $order->update([
                'status' => $request->status
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
