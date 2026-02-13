<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50); // new_order, payment_uploaded, new_chat, new_review, low_stock, order_cancelled
            $table->string('priority', 20)->default('medium'); // low, medium, high, urgent
            $table->string('title');
            $table->text('message');
            $table->string('icon', 50)->nullable();
            $table->string('color', 20)->default('blue');
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('related_type')->nullable();
            $table->string('action_url')->nullable();
            $table->string('action_text', 50)->default('Lihat');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->foreignId('read_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_read', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
