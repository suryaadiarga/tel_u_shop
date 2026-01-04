<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use App\Services\QueryService;
use Exception;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk milik merchant yang sedang login.
     */
    public function index(Request $request)
    {
        try {
            $query = Product::where('merchant_id', $request->user()->id);

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where('name', 'like', '%' . $search . '%');
            }

            if ($request->filled('category')) {
                $query->where('category', $request->input('category'));
            }

            if ($request->has('is_available')) {
                $query->where('is_available', filter_var($request->input('is_available'), FILTER_VALIDATE_BOOLEAN));
            }

            $perPage = QueryService::perPage($request);
            [$sortBy, $sortOrder] = QueryService::sort($request, ['name', 'price', 'stock', 'created_at']);

            $products = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $products
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan produk baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|string',
            'prep_time' => 'required|integer|min:1',
            'is_available' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
            }

            $product = Product::create([
                'merchant_id' => $request->user()->id,
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'stock' => $request->input('stock'),
                'image_url' => $imagePath,
                'category' => $request->category,
                'prep_time' => $request->prep_time,
                'is_available' => $request->is_available,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Produk berhasil ditambahkan.',
                'data' => $product
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memperbarui data produk.
     */
    public function update(Request $request, $id)
    {
        $product = Product::where('id', $id)
            ->where('merchant_id', $request->user()->id)
            ->firstOrFail();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'category' => 'sometimes|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            if ($request->hasFile('image')) {
                if ($product->image_url) {
                    Storage::disk('public')->delete($product->image_url);
                }
                $product->image_url = $request->file('image')->store('products', 'public');
            }

            $product->update($request->only(['name', 'description', 'price', 'stock', 'category']));

            return response()->json([
                'status' => 'success',
                'message' => 'Produk berhasil diperbarui.',
                'data' => $product
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus produk.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $product = Product::where('id', $id)
                ->where('merchant_id', $request->user()->id)
                ->firstOrFail();

            if ($product->image_url) {
                Storage::disk('public')->delete($product->image_url);
            }

            $product->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Produk berhasil dihapus.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
