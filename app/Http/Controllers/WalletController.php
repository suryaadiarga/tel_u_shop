<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $txs = $user->walletTransactions()->latest()->get();
        return view('wallet', compact('user', 'txs'));
    }

    public function topup(Request $request)
    {
        $data = $request->validate(['amount' => ['required', 'integer', 'min:1000', 'max:10000000']]);
        $user = auth()->user();

        \DB::transaction(function () use ($user, $data) {
            $user->increment('ewallet_balance', $data['amount']);
            $user->walletTransactions()->create([
                'title' => 'Topup',
                'amount' => $data['amount'],
            ]);
        });

        return back()->with('toast', 'Topup berhasil.');
    }
}
