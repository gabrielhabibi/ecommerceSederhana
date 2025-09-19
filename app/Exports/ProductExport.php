<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Product::with('category')->get()->map(function ($product) {
            return [
                'ID'          => $product->id,
                'Nama Produk' => $product->name,
                'Deskripsi'   => $product->description,
                'Harga'       => $product->price,
                'Stok'        => $product->stock,
                'Kategori'    => optional($product->category)->categories,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Produk',
            'Deskripsi',
            'Harga',
            'Stok',
            'Kategori',
        ];
    }
}
