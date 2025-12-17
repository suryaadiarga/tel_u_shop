<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Get reviews for a product
     */
    public function index(Request $request, Product $product)
    {
        $reviews = $product->reviews()->with('user:id,name')->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $reviews
        ]);
    }

    /**
     * Create a new review
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        // Check if user already reviewed this product
        $existingReview = $request->user()->reviews()->where('product_id', $product->id)->first();

        if ($existingReview) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already reviewed this product'
            ], 422);
        }

        $review = Review::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Review submitted successfully',
            'data' => $review->load('user:id,name')
        ], 201);
    }

    /**
     * Get a specific review
     */
    public function show(Request $request, Review $review)
    {
        // Check if user owns this review
        if ($review->user_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $review->load(['user:id,name', 'product:id,name'])
        ]);
    }

    /**
     * Update a review
     */
    public function update(Request $request, Review $review)
    {
        // Check if user owns this review
        if ($review->user_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $review->update($request->only(['rating', 'comment']));

        return response()->json([
            'status' => 'success',
            'message' => 'Review updated successfully',
            'data' => $review->load('user:id,name')
        ]);
    }

    /**
     * Delete a review
     */
    public function destroy(Request $request, Review $review)
    {
        // Check if user owns this review
        if ($review->user_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Review deleted successfully'
        ]);
    }

    /**
     * Get user's reviews
     */
    public function myReviews(Request $request)
    {
        $reviews = $request->user()->reviews()->with('product:id,name')->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $reviews
        ]);
    }
}
