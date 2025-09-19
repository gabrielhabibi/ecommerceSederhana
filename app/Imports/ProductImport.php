<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            0 => new ProductSheetImport(), // hanya baca sheet pertama (index 0)
        ];
    }
}

class ProductSheetImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['name'])) {
            return null;
        }

        // cek kategori → bisa pakai id atau nama
        $categoryId = null;
        if (isset($row['id_categories']) && is_numeric($row['id_categories'])) {
            $categoryId = $row['id_categories'];
        } elseif (!empty($row['id_categories'])) {
            $category = Category::where('categories', $row['id_categories'])->first();
            if ($category) {
                $categoryId = $category->id;
            }
        }

        return new Product([
            'name'        => $row['name'],
            'description' => $row['description'],
            'price'       => $row['price'],
            'stock'       => $row['stock'],
            'id_categories' => $categoryId,
        ]);
    }
}