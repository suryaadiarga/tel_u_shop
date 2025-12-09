<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * List semua produk milik merchant.
     */
    public function index()
    {
        $products = Product::where('merchant_id', Auth::id())->latest()->get();

        return response()->json([
            'data' => $products
        ]);
    }

    /**
     * Tambah produk baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:1000',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::create([
            'merchant_id' => Auth::id(),
            'name' => $validated['name'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
        ]);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan.',
            'data' => $product
        ]);
    }

    /**
     * Update produk.
     */
    public function update(Request $request, $id)
    {
        $product = Product::where('merchant_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:1000',
            'stock' => 'sometimes|integer|min:0',
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'Produk berhasil diperbarui.',
            'data' => $product
        ]);
    }

    /**
     * Hapus produk.
     */
    public function destroy($id)
    {
        $product = Product::where('merchant_id', Auth::id())->findOrFail($id);
        $product->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus.'
        ]);
    }
}