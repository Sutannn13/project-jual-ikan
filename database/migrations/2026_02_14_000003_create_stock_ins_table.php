<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->comment('Admin yang menambah stok');
            $table->decimal('qty', 10, 2)->comment('Jumlah stok masuk (Kg)');
            $table->decimal('stok_sebelum', 10, 2)->comment('Stok sebelum penambahan');
            $table->decimal('stok_sesudah', 10, 2)->comment('Stok sesudah penambahan');
            $table->decimal('harga_modal', 12, 2)->nullable()->comment('Harga modal per Kg untuk batch ini');
            $table->string('supplier')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ins');
    }
};
