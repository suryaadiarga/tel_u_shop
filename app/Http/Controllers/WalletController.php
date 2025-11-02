<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $txs = $user->walletTransactions()->latest()->get();
        return view('wallet.index', compact('user', 'txs'));
    }
}
