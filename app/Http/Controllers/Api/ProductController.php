<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET /api/products
    public function index(Request $request)
    {
        // Jika user input category_name di query string
        if ($request->has('category_name')) {
            return $this->filterByCategory($request->category_name);
        }

        // Jika tidak ada filter → tampilkan semua produk
        $products = Product::with(['category', 'images'])->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    // Detail produk
    public function show($id)
    {
        $product = Product::with(['category', 'images'])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    // 🔹 Private method: filter by kategori
    private function filterByCategory($categoryName)
    {
        $category = Category::where('categories', 'LIKE', '%' . $categoryName . '%')->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori yang kamu cari belum ada'
            ], 404);
        }

        $products = Product::with(['category', 'images'])
            ->where('id_categories', $category->id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
