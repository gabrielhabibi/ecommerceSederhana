<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CategoryExport;
use App\Imports\CategoryImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categories = Category::when($search, function($query, $search) {
            return $query->where('id', $search) // cari berdasarkan ID persis
                        ->orWhere('categories', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
        })->paginate(10);

        return view('admin.categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'categories' => 'required|string|max:255|unique:categories,categories',
            'description' => 'nullable|string',
        ]);

        Category::create($request->only('categories', 'description'));
        return redirect()->route('categories.index')->with('success', __('categories.success_create'));
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'categories' => 'required|string|max:255|unique:categories,categories,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($request->only('categories', 'description'));
        return redirect()->route('categories.index')->with('success', __('categories.success_update'));
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', __('categories.success_delete'));
    }

    public function downloadTemplate()
    {
        $template = new class implements FromArray, WithHeadings {
            public function array(): array
            {
                return []; // kosong
            }

            public function headings(): array
            {
                return [
                    'name',
                    'description',
                    'price',
                    'stock',
                    'category_id',
                    'image_base64'
                ];
            }
        };

        return Excel::download($template, 'template_products.xlsx');
    }
}