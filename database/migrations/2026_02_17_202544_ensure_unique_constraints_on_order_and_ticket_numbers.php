<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Defense-in-depth: Ensure UNIQUE constraints exist at database level
     * to prevent race conditions even if application-level checks fail.
     */
    public function up(): void
    {
        // Check and add unique index for orders.order_number if not exists
        $orderIndexExists = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.statistics 
            WHERE table_schema = DATABASE() 
            AND table_name = 'orders' 
            AND index_name = 'orders_order_number_unique'
        ");

        if (!$orderIndexExists || $orderIndexExists[0]->count == 0) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unique('order_number');
            });
        }

        // Check and add unique index for support_tickets.ticket_number if not exists
        $ticketIndexExists = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.statistics 
            WHERE table_schema = DATABASE() 
            AND table_name = 'support_tickets' 
            AND index_name = 'support_tickets_ticket_number_unique'
        ");

        if (!$ticketIndexExists || $ticketIndexExists[0]->count == 0) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->unique('ticket_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: We don't drop the indexes here because they might have been
        // created by the original migrations. This migration is defensive only.
    }
};
