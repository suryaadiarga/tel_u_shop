<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * List semua order (admin).
     */
    public function index()
    {
        $orders = Order::with('items', 'user')->latest()->get();

        return response()->json([
            'data' => $orders
        ]);
    }

    /**
     * Form create order (resource stub).
     */
    public function create()
    {
        return response()->json([
            'message' => 'Not implemented: create order form (admin).'
        ], 200);
    }

    /**
     * Simpan order baru (resource stub).
     */
    public function store(Request $request)
    {
        return response()->json([
            'message' => 'Not implemented: store order (admin).'
        ], 200);
    }

    /**
     * Detail order tertentu.
     */
    public function show($id)
    {
        $order = Order::with('items', 'user')->findOrFail($id);

        return response()->json([
            'data' => $order
        ]);
    }

    /**
     * Form edit order (resource stub).
     */
    public function edit($id)
    {
        $order = Order::findOrFail($id);

        return response()->json([
            'message' => 'Not implemented: edit order form (admin).',
            'data' => $order
        ], 200);
    }

    /**
     * Update order (resource stub).
     */
    public function update(Request $request, $id)
    {
        return response()->json([
            'message' => 'Not implemented: update order (admin).'
        ], 200);
    }

    /**
     * Hapus order (resource stub).
     */
    public function destroy($id)
    {
        return response()->json([
            'message' => 'Not implemented: destroy order (admin).'
        ], 200);
    }

    /**
     * Update status order (custom endpoint).
     * Route: PUT /api/admin/orders/{id}/status
     * Route: PATCH /admin/orders/{id}/status (jika web explicit route dipakai)
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
            'message' => 'Status order diperbarui.',
            'data' => $order
        ]);
    }
}
