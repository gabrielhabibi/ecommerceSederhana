<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'paid', 'failed', 'complete', 'canceled','deny', 'expire'])->default('pending');
            $table->decimal('total_price', 12, 2)->default(0);
            $table->string('payment_method')->nullable(); // contoh: bank_transfer, gopay, qris
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
