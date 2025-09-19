<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    // Nama tabel (opsional jika tabelnya memang "categories")
    protected $table = 'categories';

    // Field yang boleh diisi
    protected $fillable = ['categories', 'description'];

    public function products()
    {
        return $this->hasMany(Product::class, 'id_categories');
    }
}
