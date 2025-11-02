<?php

namespace App\Http\Controllers;

use App\Models\Order;

class ActivityController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')->latest()->get();
        return view('activity.index', compact('orders'));
    }
}
