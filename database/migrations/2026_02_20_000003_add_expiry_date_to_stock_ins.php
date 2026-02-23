<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->date('expiry_date')->nullable()->after('catatan')
                  ->comment('Tanggal kedaluwarsa stok ikan (penting untuk ikan segar)');
            $table->boolean('expiry_notified')->default(false)->after('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->dropColumn(['expiry_date', 'expiry_notified']);
        });
    }
};
