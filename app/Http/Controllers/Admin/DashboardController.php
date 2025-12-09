<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'title' => 'Admin Dashboard',
            'stats' => [
                'users' => User::count(),
                'orders' => Order::count(),
            ]
        ]);
    }
}