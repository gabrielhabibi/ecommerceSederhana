<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderExport;
use PDF;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OrderStatusChanged;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        // Filter tanggal
        $from = $request->from ? Carbon::parse($request->from)->startOfDay() : Carbon::now()->subWeek()->startOfDay();
        $to   = $request->to ? Carbon::parse($request->to)->endOfDay() : Carbon::now()->endOfDay();

        $query->whereBetween('created_at', [$from, $to]);

        // Search tambahan berdasarkan Order ID atau User name
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                ->orWhereHas('user', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }

        $orders = $query->get();

        return view('admin.orders.index', compact('orders', 'from', 'to'));
    }

    public function show($id)
    {
        // Ambil order spesifik beserta user + items + product
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,paid,failed,complete,canceled,deny,expire',
        ]);

        // 🔒 Pembatasan: jika status saat ini 'paid'
        if ($order->status === 'paid' && !in_array($request->status, ['complete', 'canceled'])) {
            return back()->with('error', 'Status paid hanya bisa diubah ke complete atau canceled.');
        }

        // ✅ Update status
        $order->status = $request->status;
        $order->save();

        // 🚀 Notifikasi ke user
        if ($order->user) {
            $order->user->notify(new \App\Notifications\OrderStatusChanged($order));
        }

        // 🚀 Notifikasi ke semua admin & super admin
        $admins = User::whereHas('role', function ($q) {
            $q->whereIn('role_name', ['admin', 'super admin']);
        })->get();

        if ($admins->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\OrderStatusChanged($order));
        }

        return back()->with('success', 'Status berhasil diperbarui dan notifikasi terkirim.');
    }
}
