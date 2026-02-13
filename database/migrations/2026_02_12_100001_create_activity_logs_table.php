<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable(); // snapshot in case user deleted
            $table->string('user_role', 20)->nullable();
            $table->string('action', 50); // created, updated, deleted, verified, rejected, cancelled, login, etc.
            $table->string('model', 100)->nullable(); // Order, Produk, User, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('description');
            $table->json('changes')->nullable(); // {old: {}, new: {}}
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['model', 'model_id']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
