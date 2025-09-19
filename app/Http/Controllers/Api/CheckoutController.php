<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Facades\DB;
use App\Notifications\NewOrder;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $user = $request->user();

        // Validasi
        $request->validate([
            'cart_ids' => 'array',
            'cart_ids.*' => 'exists:carts,id'
        ]);

        // Ambil cart items
        $cartItemsQuery = $user->cartItems()->with('product:id,price,name');

        if ($request->has('cart_ids') && count($request->cart_ids) > 0) {
            $cartItemsQuery->whereIn('id', $request->cart_ids);
        }

        $cartItems = $cartItemsQuery->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No cart items found'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Hitung total harga
            $totalPrice = $cartItems->sum(fn($item) => $item->quantity * $item->product->price);

            // Buat order
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'total_price' => $totalPrice
            ]);

            // Buat order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            // Hapus cart yang dibeli
            if ($request->has('cart_ids') && count($request->cart_ids) > 0) {
                $user->cartItems()->whereIn('id', $request->cart_ids)->delete();
            } else {
                $user->cartItems()->delete();
            }

            DB::commit();

            // --- MIDTRANS CONFIG ---
            Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = false; // true kalau sudah live
            Config::$isSanitized  = true;
            Config::$is3ds        = true;

            // ✅ order_id unik untuk Midtrans (hindari bentrok kalau DB di-reset)
            $midtransOrderId = $order->id . '-' . uniqid();

            $midtrans_params = [
                'transaction_details' => [
                    'order_id'     => $midtransOrderId,   // << pakai yang unik
                    'gross_amount' => $order->total_price,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email'      => $user->email,
                ],
            ];

            $snapToken = Snap::getSnapToken($midtrans_params);

            return response()->json([
                'success'     => true,
                'order_id'    => $order->id,       // ID asli di DB kamu
                'snap_token'  => $snapToken,
                // 'midtrans_order_id' => $midtransOrderId, // kalau mau dipakai di FE
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Checkout failed: '.$e->getMessage()
            ], 500);
        }
    }

    public function showCheckoutPage(Request $request, $orderId)
    {
        $order = \App\Models\Order::with('items.product', 'user')->findOrFail($orderId);

        // --- MIDTRANS CONFIG ---
        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false; // true kalau sudah live
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        // ✅ Generate order_id unik juga di halaman ini (kalau kamu pakai view ini)
        $midtransOrderId = $order->id . '-' . uniqid();

        return view('checkout-page', [
            'order'     => $order,
            'snapToken' => \Midtrans\Snap::getSnapToken([
                'transaction_details' => [
                    'order_id'     => $midtransOrderId,   // << pakai yang unik
                    'gross_amount' => $order->total_price,
                ],
                'customer_details' => [
                    'first_name' => $order->user->name,
                    'email'      => $order->user->email,
                ],
            ]),
        ]);
    }
}