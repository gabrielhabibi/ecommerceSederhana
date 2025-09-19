<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();

        // Grouping orders per hari (misalnya 7 hari terakhir)
        $recentOrders = Order::with('user')
            ->selectRaw('DATE(created_at) as order_date')
            ->selectRaw('COUNT(id) as total_orders')
            ->selectRaw('SUM(total_price) as total_amount')
            ->selectRaw('MAX(created_at) as latest_time') // ambil jam terakhir untuk urutkan
            ->groupBy('order_date')
            ->orderByDesc('latest_time')
            ->take(7)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalProducts',
            'totalOrders',
            'recentOrders'
        ));
    }
}
