<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $merchant = Auth::user();

        // Ambil data ringkasan untuk dashboard merchant
        $ordersCount = Order::where('merchant_id', $merchant->id)->count();
        $productsCount = Product::where('merchant_id', $merchant->id)->count();
        $latestOrders = Order::where('merchant_id', $merchant->id)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'title' => 'Merchant Dashboard',
            'merchant' => $merchant->only(['id', 'name', 'email']),
            'stats' => [
                'orders' => $ordersCount,
                'products' => $productsCount,
            ],
            'latest_orders' => $latestOrders,
        ]);
    }
}