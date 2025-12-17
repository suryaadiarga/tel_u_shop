<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletController extends Controller
{
    /**
     * Mengambil saldo dompet saat ini.
     */
    public function balance(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'balance' => $user->wallet_balance,
                    'formatted' => 'Rp ' . number_format($user->wallet_balance, 0, ',', '.'),
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil saldo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Proses Top Up Saldo.
     */
    public function topup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        try {
            DB::beginTransaction();

            $user = $request->user();
            $amount = (int) $request->amount;

            // Tambah saldo user
            $user->increment('wallet_balance', $amount);

            // Catat transaksi
            WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'title' => 'Top Up Saldo',
                'type' => 'topup',
                'description' => 'Top up saldo dompet sebesar Rp ' . number_format($amount, 0, ',', '.'),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Top up berhasil.',
                'data' => [
                    'current_balance' => $user->wallet_balance,
                    'formatted' => 'Rp ' . number_format($user->wallet_balance, 0, ',', '.'),
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal melakukan top up.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil riwayat transaksi dompet.
     */
    public function transactions(Request $request)
    {
        try {
            $user = $request->user();

            $transactions = WalletTransaction::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'status' => 'success',
                'data' => $transactions
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil riwayat transaksi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
