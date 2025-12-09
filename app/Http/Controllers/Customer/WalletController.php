<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WalletTransaction;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'error'   => 'Unauthenticated',
                'message' => 'Silakan login terlebih dahulu.'
            ], 401);
        }

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $totalIn  = $transactions->where('type', 'topup')->sum('amount');
        $totalOut = $transactions->where('type', 'payment')->sum('amount');

        return response()->json([
            'balance'      => $user->wallet_balance,
            'transactions' => $transactions,
            'meta'         => [
                'count'     => $transactions->count(),
                'total_in'  => $totalIn,
                'total_out' => $totalOut,
                'user'      => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
            ],
        ]);
    }

    public function balance()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'error'   => 'Unauthenticated',
                'message' => 'Silakan login terlebih dahulu.'
            ], 401);
        }

        return response()->json([
            'balance' => $user->wallet_balance,
        ]);
    }

    public function transactions()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'error'   => 'Unauthenticated',
                'message' => 'Silakan login terlebih dahulu.'
            ], 401);
        }

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'data' => $transactions,
            'meta' => [
                'count'     => $transactions->count(),
                'total_in'  => $transactions->where('type', 'topup')->sum('amount'),
                'total_out' => $transactions->where('type', 'payment')->sum('amount'),
            ]
        ]);
    }

    public function topup(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'error'   => 'Unauthenticated',
                'message' => 'Silakan login terlebih dahulu.'
            ], 401);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        WalletTransaction::create([
            'user_id'     => $user->id,
            'type'        => 'topup',
            'amount'      => $validated['amount'],
            'title'       => 'Top‑up',
            'description' => 'Top‑up saldo wallet',
            'order_id'    => null,
        ]);

        $user->increment('wallet_balance', $validated['amount']);
        $user->refresh();

        return response()->json([
            'message' => 'Top‑up berhasil.',
            'balance' => $user->wallet_balance,
        ]);
    }
}
