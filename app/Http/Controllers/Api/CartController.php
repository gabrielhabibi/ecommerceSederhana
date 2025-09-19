<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    /**
     * Display user's cart items
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $cartItems = $user->cartItems()
                ->with(['product' => function($query) {
                    $query->select('id', 'name', 'price', 'stock', 'id_categories')
                          ->with('category:id,categories');
                }])
                ->get();

            $totalItems = $cartItems->sum('quantity');
            $totalPrice = $cartItems->sum(fn($item) => $item->quantity * $item->product->price);

            return response()->json([
                'success' => true,
                'message' => $cartItems->isEmpty() ? 'Cart is empty' : 'Cart retrieved successfully',
                'data' => [
                    'items' => $cartItems,
                    'summary' => [
                        'total_items' => $totalItems,
                        'total_price' => $totalPrice,
                        'total_unique_items' => $cartItems->count()
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Cart index error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cart items',
                'error'   => $e->getMessage(), // tambahin biar kelihatan error asli
            ], 500);
        }
    }

    /**
     * Add item to cart
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 401);
            }

            $items = $request->input('items'); // ambil array items
            if (!is_array($items) || empty($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request harus berupa array produk'
                ], 422);
            }

            $results   = [];
            $hasError  = false;

            foreach ($items as $item) {
                // Validasi field wajib
                if (empty($item['product_id']) || empty($item['quantity'])) {
                    $results[] = [
                        'product_id' => $item['product_id'] ?? null,
                        'status'     => 'error',
                        'message'    => 'Item tidak valid (wajib ada product_id dan quantity)',
                    ];
                    $hasError = true;
                    continue;
                }

                $product = Product::lockForUpdate()->find($item['product_id']);
                if (!$product) {
                    $results[] = [
                        'product_id' => $item['product_id'],
                        'status'     => 'error',
                        'message'    => "Produk tidak ditemukan",
                    ];
                    $hasError = true;
                    continue;
                }

                // Validasi stok
                $quantity = (int) $item['quantity'];
                if ($product->stock < $quantity) {
                    $results[] = [
                        'product_id'         => $product->id,
                        'status'             => 'error',
                        'message'            => 'Stok produk tidak mencukupi',
                        'available_stock'    => $product->stock,
                        'requested_quantity' => $quantity,
                    ];
                    $hasError = true;
                    continue;
                }

                // Masukkan ke cart (kalau ada → tambah, kalau belum → mulai dari 0)
                foreach ($request->items as $item) {
                    $cart = Cart::where('user_id', auth()->id())
                                ->where('product_id', $item['product_id'])
                                ->first();

                    if ($cart) {
                        // Kalau produk sudah ada → tambahkan sesuai request
                        $cart->quantity += $item['quantity'];
                        $cart->save();
                    } else {
                        // Kalau produk belum ada → pakai persis quantity request
                        Cart::create([
                            'user_id'   => auth()->id(),
                            'product_id'=> $item['product_id'],
                            'quantity'  => $item['quantity'],
                        ]);
                    }
                }

                $results[] = [
                    'product_id' => $product->id,
                    'status'     => 'success',
                    'message'    => 'Produk berhasil ditambahkan ke keranjang',
                    'cart'       => $cart,
                ];
            }

            // Kalau ada produk yang gagal → rollback
            if ($hasError) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Beberapa produk gagal diproses',
                    'data'    => $results,
                ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Semua produk berhasil ditambahkan ke keranjang',
                'data'    => $results,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cart Store Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update cart items quantity
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $items = $request->all(); // array langsung dari body JSON
        $results = [];
        $successCount = 0;

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);

            if (!$product) {
                $results[] = [
                    'product_id' => $item['product_id'],
                    'status' => 'error',
                    'message' => "Produk dengan ID {$item['product_id']} tidak ditemukan"
                ];
                continue;
            }

            $cart = Cart::where('user_id', $user->id)
                ->where('product_id', $item['product_id'])
                ->first();

            if (!$cart) {
                $results[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'status' => 'error',
                    'message' => "Produk {$product->name} tidak ada di keranjang"
                ];
                continue;
            }

            // cek stok
            if ($product->stock < $item['quantity']) {
                $results[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'status' => 'error',
                    'message' => 'Stok produk tidak mencukupi',
                    'available_stock' => $product->stock,
                    'requested_quantity' => $item['quantity']
                ];
                continue;
            }

            // update cart
            $cart->quantity = $item['quantity'];
            $cart->save();

            $successCount++;
            $results[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'status' => 'success',
                'message' => 'Quantity berhasil diperbarui',
                'cart' => $cart
            ];
        }

        // cek apakah ada yg berhasil
        if ($successCount === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada produk yang berhasil diperbarui',
                'data' => $results
            ], 400); // 400 bad request / bisa juga 200 kalau mau tetap normal
        }

        return response()->json([
            'success' => true,
            'message' => 'Update cart selesai diproses',
            'data' => $results
        ]);
    }

    public function updateSingle(Request $request, $id)
    {
        $user = $request->user();

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // cari cart item berdasarkan id & user
        $cart = Cart::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Item keranjang tidak ditemukan'
            ], 404);
        }

        $product = Product::find($cart->product_id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        // cek stok
        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stok produk tidak mencukupi',
                'available_stock' => $product->stock,
                'requested_quantity' => $request->quantity
            ], 400);
        }

        // update quantity
        $cart->quantity = $request->quantity;
        $cart->save();

        return response()->json([
            'success' => true,
            'message' => 'Quantity produk berhasil diperbarui',
            'data' => $cart
        ]);
    }

    /**
     * Remove item from cart
     */
    public function destroy($id)
    {
        $user = auth()->user();

        // Cari item cart berdasarkan cart_id dan user
        $cartItem = Cart::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item keranjang dengan Cart ID ' . $id . ' tidak ditemukan atau tidak dimiliki oleh user.'
            ], 404);
        }

        // Simpan nama produk sebelum dihapus
        $productName = $cartItem->product->name ?? 'Produk tidak diketahui';

        // Hapus item
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item keranjang untuk produk ' . $productName . ' berhasil dihapus.'
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $userId = auth()->id();

        $validated = $request->validate([
            'cart_ids' => 'required|array',
            'cart_ids.*' => 'integer|exists:carts,id',
        ]);

        $cartIds = $validated['cart_ids'];

        // Ambil data cart milik user yang cocok dengan cart_ids
        $carts = Cart::where('user_id', $userId)
                    ->whereIn('id', $cartIds)
                    ->get();

        $deletedCount = 0;
        $notFound = [];

        foreach ($cartIds as $cartId) {
            $cart = $carts->firstWhere('id', $cartId);

            if ($cart) {
                $cart->delete();
                $deletedCount++;
            } else {
                $notFound[] = $cartId;
            }
        }

        // Minimal harus ada 2 yang berhasil dihapus
        if ($deletedCount >= 2) {
            return response()->json([
                'status' => 'success',
                'message' => "Berhasil menghapus {$deletedCount} item dari keranjang.",
                'not_deleted' => $notFound,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Minimal 2 item valid harus dihapus agar operasi dianggap berhasil.',
                'deleted_count' => $deletedCount,
                'not_deleted' => $notFound,
            ], 400);
        }
    }

    /**
     * Clear all items from cart
     */
    public function clear(Request $request)
    {
        $userId = $request->user()->id;

        // hapus semua cart berdasarkan user login
        Cart::where('user_id', $userId)->delete();

        return response()->json([
            'message' => 'Cart berhasil dikosongkan'
        ], 200);
    }

    /**
     * Get cart summary (total items, total price)
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $cartItems = $user->cartItems()->with('product:id,price')->get();

            $totalItems = $cartItems->sum('quantity');
            $totalPrice = $cartItems->sum(fn($item) => $item->quantity * $item->product->price);

            return response()->json([
                'success' => true,
                'message' => 'Cart summary retrieved successfully',
                'data' => [
                    'total_items' => $totalItems,
                    'total_price' => $totalPrice,
                    'total_unique_items' => $cartItems->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Cart summary error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get cart summary'
            ], 500);
        }
    }
}