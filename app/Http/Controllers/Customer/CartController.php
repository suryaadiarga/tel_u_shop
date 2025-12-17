<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Exception;

class CartController extends Controller
{
    /**
     * Menampilkan isi keranjang belanja customer yang sedang login.
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            // Ambil atau buat cart untuk user ini
            $cart = Cart::where('user_id', $user->id)->first();

            if (!$cart) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'items' => [],
                        'total' => 0,
                    ]
                ], 200);
            }

            $items = CartItem::with('product')
                ->where('cart_id', $cart->id)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product' => $item->product,
                        'qty' => $item->qty,
                        'price_snapshot' => $item->price_snapshot,
                        'subtotal' => $item->qty * $item->price_snapshot,
                    ];
                });

            $total = $cart->total();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'items' => $items,
                    'total' => $total,
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data keranjang.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menambahkan produk ke dalam keranjang.
     */
    public function add(Request $request, $productId)
    {
        try {
            $product = Product::findOrFail($productId);

            $request->validate([
                'qty' => 'required|integer|min:1|max:' . $product->stock,
            ]);

            $user = $request->user();

            // Dapatkan atau buat cart untuk user ini
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            // Cek apakah produk sudah ada di keranjang
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                // Update quantity jika sudah ada
                $cartItem->qty += $request->qty;
                $cartItem->save();
            } else {
                // Buat item baru jika belum ada
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'qty' => $request->qty,
                    'price_snapshot' => $product->price,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
                'data' => [
                    'id' => $cartItem->id,
                    'product_id' => $cartItem->product_id,
                    'qty' => $cartItem->qty,
                    'price_snapshot' => $cartItem->price_snapshot,
                    'subtotal' => $cartItem->qty * $cartItem->price_snapshot,
                ]
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan produk ke keranjang.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memperbarui jumlah (quantity) produk di keranjang.
     */
    public function updateQty(Request $request, $itemId)
    {
        try {
            $cartItem = CartItem::findOrFail($itemId);

            // Verify ownership
            if ($cartItem->cart->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 403);
            }

            $request->validate([
                'qty' => 'required|integer|min:1|max:' . $cartItem->product->stock,
            ]);

            $cartItem->update(['qty' => $request->qty]);

            return response()->json([
                'status' => 'success',
                'message' => 'Jumlah produk berhasil diperbarui.',
                'data' => [
                    'id' => $cartItem->id,
                    'qty' => $cartItem->qty,
                    'subtotal' => $cartItem->qty * $cartItem->price_snapshot,
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui jumlah produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus satu item dari keranjang.
     */
    public function remove(Request $request, $itemId)
    {
        try {
            $cartItem = CartItem::findOrFail($itemId);

            // Verify ownership
            if ($cartItem->cart->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 403);
            }

            $cartItem->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Produk berhasil dihapus dari keranjang.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus produk dari keranjang.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengosongkan seluruh isi keranjang.
     */
    public function clear(Request $request)
    {
        try {
            $user = $request->user();
            $cart = Cart::where('user_id', $user->id)->first();

            if ($cart) {
                CartItem::where('cart_id', $cart->id)->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Keranjang berhasil dikosongkan.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengosongkan keranjang.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
