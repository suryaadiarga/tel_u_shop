<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Review;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class ActivityController extends Controller
{
    /**
     * Menampilkan riwayat pesanan (Aktivitas) pengguna.
     * Mendukung filter status, rentang tanggal, pencarian, dan paginasi.
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $query = Order::with(['items.product'])
                ->where('user_id', $user->id);

            // 1. Filter berdasarkan status (pending, completed, cancelled, dsb)
            if ($request->has('status') && $request->input('status') !== 'all') {
                $query->where('status', $request->input('status'));
            }

            // 2. Filter berdasarkan rentang tanggal
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('created_at', [
                    $request->input('start_date') . ' 00:00:00',
                    $request->input('end_date') . ' 23:59:59'
                ]);
            }

            // 3. Pencarian berdasarkan Nama Produk atau ID Pesanan
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'LIKE', "%{$search}%")
                        ->orWhereHas('items.product', function ($sq) use ($search) {
                            $sq->where('name', 'LIKE', "%{$search}%");
                        });
                });
            }

            // 4. Statistik Ringkas (Sekarang termasuk Total Pengeluaran)
            $stats = [
                'total_orders' => Order::where('user_id', $user->id)->count(),
                'pending' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
                'completed' => Order::where('user_id', $user->id)->where('status', 'completed')->count(),
                'total_spent' => Order::where('user_id', $user->id)->where('status', 'completed')->sum('total_amount'),
            ];

            // 5. Paginasi
            $perPage = $request->get('per_page', 10);
            $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Riwayat aktivitas berhasil diambil.',
                'stats' => $stats,
                'data' => $orders
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil riwayat aktivitas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail spesifik dari satu aktivitas/pesanan.
     */
    public function show(Request $request, $id)
    {
        try {
            $order = Order::with(['items.product.user', 'items.review'])
                ->where('user_id', $request->user()->id)
                ->where('id', $id)
                ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'data' => $order
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Detail aktivitas tidak ditemukan.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mendapatkan log pelacakan (tracking) status pesanan.
     * Berguna untuk menampilkan timeline di frontend.
     */
    public function track(Request $request, $id)
    {
        try {
            $user = $request->user();
            $order = Order::where('user_id', $user->id)->where('id', $id)->firstOrFail();

            // Simulasi data tracking berdasarkan status saat ini
            // Dalam sistem nyata, ini biasanya diambil dari tabel 'order_status_logs'
            $tracking = [
                ['status' => 'pending', 'label' => 'Pesanan Dibuat', 'time' => $order->created_at, 'completed' => true],
                ['status' => 'paid', 'label' => 'Pembayaran Berhasil', 'time' => $order->paid_at ?? null, 'completed' => in_array($order->status, ['paid', 'shipped', 'completed'])],
                ['status' => 'shipped', 'label' => 'Pesanan Dikirim/Diproses', 'time' => $order->shipped_at ?? null, 'completed' => in_array($order->status, ['shipped', 'completed'])],
                ['status' => 'completed', 'label' => 'Pesanan Selesai', 'time' => $order->completed_at ?? null, 'completed' => ($order->status === 'completed')],
            ];

            return response()->json([
                'status' => 'success',
                'order_id' => $id,
                'current_status' => $order->status,
                'timeline' => $tracking
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal melacak pesanan.'
            ], 404);
        }
    }

    /**
     * Konfirmasi bahwa pesanan telah diterima oleh pengguna.
     * Mengubah status dari 'shipped' atau 'paid' menjadi 'completed'.
     */
    public function confirmReceived(Request $request, $id)
    {
        try {
            $order = Order::where('user_id', $request->user()->id)
                ->where('id', $id)
                ->firstOrFail();

            // Validasi: Pesanan harus sudah dibayar atau dikirim untuk bisa diselesaikan
            if (!in_array($order->status, ['paid', 'shipped'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak dapat dikonfirmasi pada status ' . $order->status
                ], 422);
            }

            $order->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan telah selesai. Silakan berikan ulasan Anda!',
                'data' => $order
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengonfirmasi pesanan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Membatalkan pesanan (hanya jika status masih pending).
     * Fitur ini akan mengembalikan stok produk dan saldo wallet.
     */
    public function cancel(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $user = $request->user();
            $order = Order::with('items.product')
                ->where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            // Validasi: Hanya pesanan pending yang bisa dibatalkan
            if ($order->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak dapat dibatalkan karena status sudah ' . $order->status
                ], 422);
            }

            // 1. Kembalikan stok produk
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->qty);
                }
            }

            // 2. Kembalikan saldo Wallet
            $user->increment('wallet_balance', $order->total_amount);

            // 3. Catat transaksi pengembalian dana (Refund)
            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'topup',
                'amount' => $order->total_amount,
                'title' => 'Refund Pembatalan Pesanan',
                'description' => 'Pengembalian dana untuk pesanan #' . $order->id,
                'order_id' => $order->id,
            ]);

            // 4. Update status pesanan menjadi cancelled
            $order->update(['status' => 'cancelled']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibatalkan dan dana telah dikembalikan.',
                'data' => $order
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membatalkan pesanan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memberikan ulasan pada item pesanan yang sudah selesai.
     */
    public function review(Request $request, $orderItemId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        try {
            $user = $request->user();

            // Cari item order dan pastikan pesanan sudah completed
            $orderItem = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.user_id', $user->id)
                ->where('orders.status', 'completed')
                ->where('order_items.id', $orderItemId)
                ->select('order_items.*')
                ->first();

            if (!$orderItem) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Item tidak ditemukan atau pesanan belum selesai.'
                ], 404);
            }

            // Cek apakah sudah pernah diulas
            $exists = Review::where('order_item_id', $orderItemId)->exists();
            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda sudah memberikan ulasan untuk produk ini.'
                ], 422);
            }

            $review = Review::create([
                'user_id' => $user->id,
                'product_id' => $orderItem->product_id,
                'order_item_id' => $orderItemId,
                'rating' => $request->input('rating'),
                'comment' => $request->input('comment'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Terima kasih! Ulasan Anda telah disimpan.',
                'data' => $review
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan ulasan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memasukkan kembali item dari pesanan lama ke dalam keranjang (Reorder).
     */
    public function reorder(Request $request, $id)
    {
        try {
            $user = $request->user();
            $order = Order::with('items')->where('user_id', $user->id)->where('id', $id)->firstOrFail();

            foreach ($order->items as $item) {
                // Tambahkan ke keranjang (Asumsi menggunakan model CartItem)
                $cart = Cart::firstOrCreate(['user_id' => $user->id]);
                $cartItem = CartItem::where('cart_id', $cart->id)
                    ->where('product_id', $item->product_id)
                    ->first();

                if ($cartItem) {
                    $cartItem->increment('qty', $item->qty);
                } else {
                    CartItem::create([
                        'cart_id' => $cart->id,
                        'product_id' => $item->product_id,
                        'qty' => $item->qty,
                        'price_snapshot' => $item->price
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Item dari pesanan ini telah ditambahkan kembali ke keranjang belanja Anda.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal melakukan reorder.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghasilkan data untuk invoice (untuk dicetak di React).
     */
    public function invoice(Request $request, $id)
    {
        try {
            $order = Order::with(['items.product', 'user'])
                ->where('user_id', $request->user()->id)
                ->where('id', $id)
                ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'message' => 'Data invoice berhasil dimuat.',
                'data' => [
                    'invoice_number' => 'INV/' . $order->created_at->format('Ymd') . '/' . $order->id,
                    'order' => $order,
                    'store_info' => [
                        'name' => 'Kantin Telkom University',
                        'address' => 'Jl. Telekomunikasi No. 1, Bandung',
                        'contact' => '0812-3456-7890'
                    ]
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice tidak ditemukan.',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
