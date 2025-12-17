<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Product;
use Exception;

class ReviewController extends Controller
{
    /**
     * Menampilkan semua review untuk produk tertentu.
     */
    public function index(Request $request, Product $product)
    {
        try {
            $reviews = Review::with('user:id,name,email')
                ->where('product_id', $product->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $reviews
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil review produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Membuat review baru untuk produk.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            $user = $request->user();

            // Cek apakah user sudah pernah review produk ini
            $existingReview = Review::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda sudah memberikan review untuk produk ini.'
                ], 422);
            }

            $review = Review::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Review berhasil ditambahkan.',
                'data' => $review->load('user:id,name,email')
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan review.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan review tertentu.
     */
    public function show(Request $request, Review $review)
    {
        try {
            // Verify ownership
            if ($review->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 403);
            }

            return response()->json([
                'status' => 'success',
                'data' => $review->load(['user:id,name,email', 'product:id,name'])
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil review.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memperbarui review.
     */
    public function update(Request $request, Review $review)
    {
        $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes|nullable|string|max:1000',
        ]);

        try {
            // Verify ownership
            if ($review->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 403);
            }

            $review->update($request->only(['rating', 'comment']));

            return response()->json([
                'status' => 'success',
                'message' => 'Review berhasil diperbarui.',
                'data' => $review->load(['user:id,name,email', 'product:id,name'])
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui review.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus review.
     */
    public function destroy(Request $request, Review $review)
    {
        try {
            // Verify ownership
            if ($review->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 403);
            }

            $review->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Review berhasil dihapus.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus review.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan review yang dibuat oleh user yang sedang login.
     */
    public function myReviews(Request $request)
    {
        try {
            $user = $request->user();

            $reviews = Review::with('product:id,name,price')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $reviews
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil review Anda.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
