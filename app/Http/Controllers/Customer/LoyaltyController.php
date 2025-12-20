<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoyaltyPoint;
use App\Services\QueryService;
use Exception;

class LoyaltyController extends Controller
{
    /**
     * Get user's loyalty balance.
     */
    public function balance(Request $request)
    {
        try {
            $user = $request->user();

            $balance = LoyaltyPoint::where('user_id', $user->id)
                ->sum('points');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'balance' => $balance,
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil saldo poin loyalitas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's loyalty history.
     */
    public function history(Request $request)
    {
        try {
            $user = $request->user();

            $query = LoyaltyPoint::where('user_id', $user->id);

            if ($request->filled('type')) {
                $query->where('type', $request->input('type'));
            }

            $perPage = QueryService::perPage($request);
            [$sortBy, $sortOrder] = QueryService::sort($request, ['created_at', 'points', 'type']);

            $history = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $history
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil riwayat poin loyalitas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Redeem loyalty points.
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|min:100',
            'description' => 'required|string|max:255',
        ]);

        try {
            $user = $request->user();

            $balance = LoyaltyPoint::where('user_id', $user->id)
                ->sum('points');

            if ($balance < $request->points) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Poin loyalitas tidak mencukupi.'
                ], 400);
            }

            // Create redemption record (negative points)
            LoyaltyPoint::create([
                'user_id' => $user->id,
                'points' => -$request->points,
                'type' => 'redeemed',
                'description' => $request->description,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Poin loyalitas berhasil ditukar.',
                'data' => [
                    'redeemed_points' => $request->points,
                    'remaining_balance' => $balance - $request->points,
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menukar poin loyalitas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available rewards.
     */
    public function rewards(Request $request)
    {
        try {
            // Define available rewards
            $rewards = [
                [
                    'id' => 1,
                    'name' => 'Diskon 10%',
                    'points_required' => 500,
                    'description' => 'Diskon 10% untuk pembelian berikutnya',
                    'type' => 'discount',
                ],
                [
                    'id' => 2,
                    'name' => 'Gratis Ongkir',
                    'points_required' => 300,
                    'description' => 'Gratis ongkos kirim untuk 1 pesanan',
                    'type' => 'shipping',
                ],
                [
                    'id' => 3,
                    'name' => 'Voucher Rp 25.000',
                    'points_required' => 1000,
                    'description' => 'Voucher potongan harga Rp 25.000',
                    'type' => 'voucher',
                ],
            ];

            $user = $request->user();
            $balance = LoyaltyPoint::where('user_id', $user->id)
                ->sum('points');

            // Mark which rewards user can afford
            foreach ($rewards as &$reward) {
                $reward['can_redeem'] = $balance >= $reward['points_required'];
            }

            return response()->json([
                'status' => 'success',
                'data' => $rewards
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil daftar hadiah.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
