<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Template name e.g. "Penawaran Stok Segar"
            $table->text('pesan'); // Message body with placeholders like {nama}, {produk}, {harga}
            $table->text('deskripsi')->nullable(); // Short description of the template
            $table->string('kategori')->nullable(); // e.g. promo, restock_alert, order_confirmed
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('whatsapp_blast_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->nullable()->constrained('whatsapp_templates')->nullOnDelete();
            $table->foreignId('sent_by')->constrained('users')->onDelete('cascade');
            $table->text('pesan_terkirim'); // Final message after variable substitution
            $table->integer('jumlah_penerima')->default(0);
            $table->json('target_phones')->nullable(); // Array of phone numbers sent to
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_blast_logs');
        Schema::dropIfExists('whatsapp_templates');
    }
};
