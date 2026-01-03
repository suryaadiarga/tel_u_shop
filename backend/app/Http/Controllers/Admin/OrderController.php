<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\QueryService;

class OrderController extends Controller
{
    /**
     * Get all orders with pagination
     */
    public function index(Request $request)
    {
        $query = Order::with(['user:id,name,email', 'items.product:id,name']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->input('start_date'), $request->input('end_date')]);
        }

        // Search by order ID or user name
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = QueryService::perPage($request, 15);
        [$sortBy, $sortOrder] = QueryService::sort($request, ['created_at', 'status', 'total_amount']);

        $orders = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    /**
     * Get a specific order
     */
    public function show(Request $request, Order $order)
    {
        return response()->json([
            'status' => 'success',
            'data' => $order->load(['user:id,name,email', 'items.product:id,name,price'])
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $order->update([
            'status' => $request->input('status')
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order status updated successfully',
            'data' => $order
        ]);
    }
}
