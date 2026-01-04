<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Notification;
use App\Services\QueryService;
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

            $query = Order::with([
                'items' => function ($query) use ($merchantId) {
                    $query->whereHas('product', function ($productQuery) use ($merchantId) {
                        $productQuery->where('merchant_id', $merchantId);
                    });
                },
                'items.product',
                'user',
            ])->whereHas('items.product', function ($query) use ($merchantId) {
                $query->where('merchant_id', $merchantId);
            });

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->input('start_date') . ' 00:00:00',
                    $request->input('end_date') . ' 23:59:59',
                ]);
            }

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where('id', 'like', '%' . $search . '%');
            }

            $perPage = QueryService::perPage($request);
            [$sortBy, $sortOrder] = QueryService::sort($request, ['created_at', 'status', 'total_amount']);

            $orders = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

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
            $previousStatus = $order->status;

            $hasAccess = $order->items()
                ->whereHas('product', function ($query) use ($merchantId) {
                    $query->where('merchant_id', $merchantId);
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

            if ($previousStatus !== $order->status) {
                Notification::create([
                    'user_id' => $order->user_id,
                    'type' => 'order_status',
                    'title' => 'Status Pesanan Diperbarui',
                    'message' => "Status pesanan #{$order->id} berubah dari {$previousStatus} ke {$order->status}.",
                    'data' => [
                        'order_id' => $order->id,
                        'from' => $previousStatus,
                        'to' => $order->status,
                    ],
                ]);
            }

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
