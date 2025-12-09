<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Tampilkan isi keranjang user.
     */
    public function index()
    {
        // Contoh: ambil cart dari session atau database
        $cart = session()->get('cart', []);

        return response()->json([
            'data' => $cart,
            'meta' => [
                'count' => count($cart),
                'total' => collect($cart)->sum(fn($item) => $item['price'] * $item['qty']),
            ]
        ]);
    }

    /**
     * Tambahkan produk ke keranjang.
     */
    public function add(Request $request, $product)
    {
        // logic add to cart
        // contoh sederhana: simpan ke session
        $cart = session()->get('cart', []);
        $cart[] = [
            'product_id' => $product,
            'qty' => $request->input('qty', 1),
            'price' => $request->input('price', 0),
        ];
        session()->put('cart', $cart);

        return response()->json([
            'message' => 'Product added to cart.',
            'data' => $cart
        ]);
    }

    /**
     * Update jumlah item di keranjang.
     */
    public function updateQty(Request $request, $item)
    {
        $cart = session()->get('cart', []);
        foreach ($cart as &$c) {
            if ($c['product_id'] == $item) {
                $c['qty'] = $request->input('qty', $c['qty']);
            }
        }
        session()->put('cart', $cart);

        return response()->json([
            'message' => 'Cart updated.',
            'data' => $cart
        ]);
    }

    /**
     * Hapus item dari keranjang.
     */
    public function remove($item)
    {
        $cart = session()->get('cart', []);
        $cart = array_filter($cart, fn($c) => $c['product_id'] != $item);
        session()->put('cart', $cart);

        return response()->json([
            'message' => 'Item removed.',
            'data' => $cart
        ]);
    }

    /**
     * Kosongkan keranjang.
     */
    public function clear()
    {
        session()->forget('cart');

        return response()->json([
            'message' => 'Cart cleared.',
            'data' => []
        ]);
    }
}