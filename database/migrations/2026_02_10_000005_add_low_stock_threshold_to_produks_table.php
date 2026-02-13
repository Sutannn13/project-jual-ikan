<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->decimal('low_stock_threshold', 8, 2)->default(10)->after('stok');
            $table->boolean('low_stock_notified')->default(false)->after('low_stock_threshold');
        });
    }

    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn(['low_stock_threshold', 'low_stock_notified']);
        });
    }
};
