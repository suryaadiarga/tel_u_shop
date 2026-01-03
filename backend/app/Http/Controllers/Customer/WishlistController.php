<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Services\QueryService;

class WishlistController extends Controller
{
    /**
     * Get user's wishlist
     */
    public function index(Request $request)
    {
        $query = $request->user()->wishlists()->with(['product.merchant']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('product', function ($productQuery) use ($search) {
                $productQuery->where('name', 'like', '%' . $search . '%');
            });
        }

        $perPage = QueryService::perPage($request);
        [$sortBy, $sortOrder] = QueryService::sort($request, ['created_at']);

        $wishlist = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $wishlist
        ]);
    }

    /**
     * Add product to wishlist
     */
    public function add(Request $request, Product $product)
    {
        if (!$product->is_available || $product->stock <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk tidak tersedia.'
            ], 422);
        }

        if ($product->merchant && (!$product->merchant->isMerchantApproved() || $product->merchant->isBanned())) {
            return response()->json([
                'status' => 'error',
                'message' => 'Merchant belum disetujui atau diblokir.'
            ], 422);
        }

        // Check if already in wishlist
        $existing = $request->user()->wishlists()->where('product_id', $product->id)->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product already in wishlist'
            ], 422);
        }

        $wishlist = $request->user()->wishlists()->create([
            'product_id' => $product->id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to wishlist',
            'data' => $wishlist->load('product')
        ]);
    }

    /**
     * Remove product from wishlist
     */
    public function remove(Request $request, Product $product)
    {
        $wishlist = $request->user()->wishlists()->where('product_id', $product->id)->first();

        if (!$wishlist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not in wishlist'
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product removed from wishlist'
        ]);
    }

    /**
     * Check if product is in wishlist
     */
    public function check(Request $request, Product $product)
    {
        $exists = $request->user()->wishlists()->where('product_id', $product->id)->exists();

        return response()->json([
            'status' => 'success',
            'data' => [
                'in_wishlist' => $exists
            ]
        ]);
    }
}
