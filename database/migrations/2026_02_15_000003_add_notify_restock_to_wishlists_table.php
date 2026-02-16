<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->boolean('notify_when_available')->default(true)->after('produk_id');
            $table->timestamp('notified_at')->nullable()->after('notify_when_available');
        });
    }

    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropColumn(['notify_when_available', 'notified_at']);
        });
    }
};
