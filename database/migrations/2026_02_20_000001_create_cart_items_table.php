<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->decimal('qty', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'produk_id']); // one row per product per user
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
