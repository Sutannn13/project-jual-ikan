<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('refund_status', ['none', 'requested', 'approved', 'rejected'])->default('none')->after('status');
            $table->text('refund_reason')->nullable()->after('refund_status');
            $table->text('refund_admin_note')->nullable()->after('refund_reason');
            $table->timestamp('refund_requested_at')->nullable()->after('refund_admin_note');
            $table->timestamp('refund_processed_at')->nullable()->after('refund_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['refund_status', 'refund_reason', 'refund_admin_note', 'refund_requested_at', 'refund_processed_at']);
        });
    }
};
