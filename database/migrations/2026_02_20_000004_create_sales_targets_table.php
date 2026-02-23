<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_targets', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['daily', 'monthly'])->default('monthly');
            $table->decimal('target_amount', 15, 2);
            $table->date('target_date'); // For daily: specific date; for monthly: first day of month
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['type', 'target_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_targets');
    }
};
