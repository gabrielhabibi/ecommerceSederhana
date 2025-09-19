<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentSuccessMail;
use App\Notifications\NewOrder;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

// ✅ Import Midtrans SDK
use Midtrans\Config;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Setup Midtrans config
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = false; // Sandbox dulu
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    public function handleNotification(Request $request)
    {
        \Log::info('Midtrans Notification Payload:', $request->all());

        try {
            $notification = $request->all();
            $orderId = $notification['order_id'] ?? null;
            $transactionStatus = $notification['transaction_status'] ?? null;

            if (!$orderId) {
                \Log::error('Order ID missing in notification');
                return response()->json(['message' => 'Order ID missing'], 400);
            }

            // Kalau order_id kamu formatnya "1-uniqid", ambil yang depan (ID asli DB)
            $orderIdParts = explode('-', $orderId);
            $realOrderId = $orderIdParts[0];

            $order = Order::with(['user', 'items.product'])->find($realOrderId);
            if (!$order) {
                \Log::error('Order not found: ' . $realOrderId);
                return response()->json(['message' => 'Order not found'], 404);
            }

            // ✅ Ambil semua admin berdasarkan relasi role
            $admins = User::whereHas('role', function ($q) {
                $q->whereIn('role_name', ['admin', 'super admin']);
            })->get();

            if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                $order->update([
                    'status'         => 'paid',
                    'payment_method' => $notification['payment_type'] ?? null,
                    'paid_at'        => now(),
                ]);

                // 🚀 Kurangi stok produk setelah pembayaran sukses
                foreach ($order->items as $item) {
                    $product = $item->product;
                    if ($product) {
                        if ($product->stock >= $item->quantity) {
                            $product->stock -= $item->quantity;
                            $product->save();
                        } else {
                            \Log::warning("Stok produk {$product->name} tidak mencukupi untuk order {$order->id}");
                        }
                    }
                }

                // 🚀 Kirim email ke user
                if ($order->user && $order->user->email) {
                    Mail::to($order->user->email)->send(new PaymentSuccessMail($order));
                }

                // 🚀 Kirim notifikasi ke admin
                Notification::send($admins, new NewOrder($order, 'success'));

            } elseif ($transactionStatus === 'pending') {
                $order->update([
                    'status'         => 'pending',
                    'payment_method' => $notification['payment_type'] ?? null,
                ]);

                Notification::send($admins, new NewOrder($order, 'pending'));

            } elseif (in_array($transactionStatus, ['deny', 'expire', 'canceled'])) {
                $order->update([
                    'status'         => 'failed',
                    'payment_method' => $notification['payment_type'] ?? null,
                ]);

                Notification::send($admins, new NewOrder($order, 'failed'));
            }

            return response()->json(['message' => 'Notification processed']);
        } catch (\Exception $e) {
            \Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['message' => 'Server error'], 500);
        }
    }
}
