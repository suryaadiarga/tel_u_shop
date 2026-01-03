<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\QueryService;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk yang tersedia untuk customer
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with('merchant:id,name')
                ->where('is_available', true)
                ->where('stock', '>', 0)
                ->whereHas('merchant', function ($merchantQuery) {
                    $merchantQuery->where('merchant_status', 'approved')
                        ->where('is_banned', false);
                });

            // Filter berdasarkan kategori jika ada
            if ($request->has('category') && $request->category) {
                $query->where('category', $request->category);
            }

            // Search berdasarkan nama produk
            if ($request->has('search') && $request->search) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $perPage = QueryService::perPage($request);
            [$sortBy, $sortOrder] = QueryService::sort($request, ['name', 'price', 'created_at']);

            $products = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

            // Format response
            $formattedProducts = $products->getCollection()->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'formatted_price' => $product->formatted_price,
                    'stock' => $product->stock,
                    'stock_status' => $product->stock_status,
                    'prep_time' => $product->prep_time,
                    'image_url' => $product->image_url,
                    'category' => $product->category,
                    'merchant' => $product->merchant ? [
                        'id' => $product->merchant->id,
                        'name' => $product->merchant->name,
                    ] : null,
                    'created_at' => $product->created_at,
                ];
            });

            $products->setCollection($formattedProducts);

            return response()->json([
                'status' => 'success',
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail produk tertentu
     */
    public function show($id)
    {
        try {
            $product = Product::with('merchant:id,name')
                ->where('is_available', true)
                ->where('stock', '>', 0)
                ->whereHas('merchant', function ($merchantQuery) {
                    $merchantQuery->where('merchant_status', 'approved')
                        ->where('is_banned', false);
                })
                ->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'formatted_price' => $product->formatted_price,
                    'stock' => $product->stock,
                    'stock_status' => $product->stock_status,
                    'prep_time' => $product->prep_time,
                    'image_url' => $product->image_url,
                    'category' => $product->category,
                    'merchant' => $product->merchant ? [
                        'id' => $product->merchant->id,
                        'name' => $product->merchant->name,
                    ] : null,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk tidak ditemukan.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mendapatkan daftar kategori produk yang tersedia
     */
    public function categories()
    {
        try {
            $categories = Product::where('is_available', true)
                ->where('stock', '>', 0)
                ->whereHas('merchant', function ($merchantQuery) {
                    $merchantQuery->where('merchant_status', 'approved')
                        ->where('is_banned', false);
                })
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category')
                ->sort()
                ->values();

            return response()->json([
                'status' => 'success',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kategori.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
