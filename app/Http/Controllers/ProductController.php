<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExport;
use App\Imports\ProductImport;
use App\Notifications\NewProduct;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                return $query->where('id', $search)
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('category', fn($q) => $q->where('categories', 'like', "%{$search}%"));
            })
            ->paginate(10);

        return view('admin.products.index', compact('products', 'search'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'required',
            'price'         => 'required|numeric',
            'stock'         => 'required|integer',
            'pictures'      => 'required',
            'pictures.*'    => 'image|mimes:jpeg,png,jpg|max:2048',
            'id_categories' => 'required|exists:categories,id'
        ]);

        // Simpan produk baru
        $product = Product::create([
            'name'          => $request->name,
            'description'   => $request->description,
            'price'         => $request->price,
            'stock'         => $request->stock,
            'id_categories' => $request->id_categories
        ]);

        // Simpan semua gambar dalam bentuk base64
        if ($request->hasFile('pictures')) {
            foreach ($request->file('pictures') as $image) {
                $imageContent = file_get_contents($image->getRealPath());
                $base64 = base64_encode($imageContent);
                $hash = hash('sha256', $imageContent);

                $product->images()->create([
                    'image' => $base64,
                    'hash'  => $hash
                ]);
            }
        }

        // Ambil semua admin & super admin, kecuali user yg login
        $recipients = User::whereHas('role', function ($q) {
                $q->whereIn('role_name', ['admin', 'super admin']);
            })
            ->where('id', '!=', Auth::id())
            ->get();

        Log::info('Recipients found:', $recipients->map(fn($u) => [
            'id'   => $u->id,
            'name' => $u->name,
            'role' => optional($u->role)->role_name,
        ])->toArray());

        if ($recipients->isEmpty()) {
            Log::warning('No recipients found for notification!');
        } else {
            Log::info('Recipients total: ' . $recipients->count());
        }

        $product = $product->fresh();
        $creator = Auth::user();

        Notification::send($recipients, new NewProduct($product, $creator));

        return redirect()->route('products.index')->with('success', __('product.success_create'));
    }

    public function edit($id)
    {
        $product = Product::with('images')->findOrFail($id);
        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'required',
            'price'         => 'required|numeric',
            'stock'         => 'required|integer',
            'id_categories' => 'required|exists:categories,id',
            'pictures.*'    => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = Product::with('images')->findOrFail($id);

        // Update data utama produk
        $product->update([
            'name'          => $request->name,
            'description'   => $request->description,
            'price'         => $request->price,
            'stock'         => $request->stock,
            'id_categories' => $request->id_categories,
        ]);

        // Tambahkan gambar baru hanya jika ada yang diunggah
        if ($request->hasFile('pictures')) {
            $existingHashes = $product->images->pluck('hash')->toArray();

            foreach ($request->file('pictures') as $image) {
                $imageContent = file_get_contents($image->getRealPath());
                $newBase64 = base64_encode($imageContent);
                $newHash = hash('sha256', $imageContent);

                if (!in_array($newHash, $existingHashes)) {
                    $product->images()->create([
                        'image' => $newBase64,
                        'hash'  => $newHash
                    ]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', __('product.success_update'));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', __('product.success_delete'));
    }

    // Export produk
    public function export()
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }

    // Download template
    public function downloadTemplate()
    {
        $export = new class implements WithMultipleSheets {
            public function sheets(): array
            {
                return [
                    new class implements FromArray, WithHeadings {
                        public function array(): array
                        {
                            return []; // sheet kosong
                        }

                        public function headings(): array
                        {
                            return [
                                'name',
                                'description',
                                'price',
                                'stock',
                                'id_categories',
                            ];
                        }
                    },
                    new class implements FromArray, WithHeadings {
                        public function array(): array
                        {
                            return Category::select('id', 'categories')->get()->toArray();
                        }

                        public function headings(): array
                        {
                            return ['id', 'categories'];
                        }
                    }
                ];
            }
        };

        return Excel::download($export, 'template_products.xlsx');
    }

    // Import produk
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new ProductImport, $request->file('file'));

        return redirect()->route('products.index')->with('success', __('product.success_import'));
    }
}
