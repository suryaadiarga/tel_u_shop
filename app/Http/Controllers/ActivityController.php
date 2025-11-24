<?php

namespace App\Http\Controllers;

use App\Models\Order;

class ActivityController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()->latest()->get();
        return view('activity', compact('orders'));
    }

}
