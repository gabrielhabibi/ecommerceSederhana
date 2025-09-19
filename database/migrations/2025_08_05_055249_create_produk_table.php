<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('categories');
            $table->longtext('description');
            $table->timestamps();
        });
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description');
            $table->decimal('price', 25, 2);
            $table->integer('stock');
            $table->foreignId('id_categories')->constrained('categories')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('product')->onDelete('cascade');
            $table->longText('image'); // base64 string
            $table->string('hash')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('product_images');
    }
};
