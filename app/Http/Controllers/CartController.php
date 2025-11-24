<?php

namespace App\Http\Controllers;

use App\Models\{Cart, CartItem, Product};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getOrCreateCart();
        $items = $cart->items()->with('product')->get();
        return view('cart', compact('items'));
    }


    public function add(Request $request, Product $product)
    {
        $request->validate(['qty' => ['nullable', 'integer', 'min:1', 'max:99']]);
        $qty = (int) ($request->qty ?: 1);

        $cart = $this->getOrCreateCart();

        $item = CartItem::firstOrNew([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);
        if (!$item->exists) {
            $item->qty = 0;
            $item->price_snapshot = $product->price;
        }
        $item->qty += $qty;
        $item->save();

        return back()->with('toast', 'Ditambahkan ke keranjang.');
    }

    public function updateQty(Request $request, CartItem $item)
    {
        $request->validate(['qty' => ['required', 'integer', 'min:1', 'max:99']]);
        $this->abortIfNotOwner($item->cart->user_id);
        $item->update(['qty' => (int) $request->qty]);
        return back()->with('toast', 'Jumlah diperbarui.');
    }

    public function remove(CartItem $item)
    {
        $this->abortIfNotOwner($item->cart->user_id);
        $item->delete();
        return back()->with('toast', 'Item dihapus.');
    }

    public function clear()
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->delete();
        return back()->with('toast', 'Keranjang dikosongkan.');
    }

    private function getOrCreateCart()
    {
        if (!Schema::hasTable('carts')) {
            abort(500, 'Tabel carts belum dibuat. Jalankan: php artisan migrate');
        }

        return auth()->user()->cart()->firstOrCreate([]);
    }

    private function abortIfNotOwner(int $ownerId): void
    {
        if ($ownerId !== auth()->id())
            abort(403);
    }
}
