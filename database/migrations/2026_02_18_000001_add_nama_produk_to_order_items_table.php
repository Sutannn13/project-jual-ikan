<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * SNAPSHOT DATA: Add product name snapshot to order_items.
 *
 * WHY: order_items currently references produk_id for the product name,
 * meaning if a product is renamed or soft-deleted, historical orders
 * would display incorrect data. This migration adds `nama_produk` to
 * store the product name at the time of purchase.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('nama_produk')->nullable()->after('produk_id')
                ->comment('Snapshot of product name at time of purchase');
        });

        // Backfill existing order items with current product names
        DB::statement('
            UPDATE order_items
            JOIN produks ON order_items.produk_id = produks.id
            SET order_items.nama_produk = produks.nama
            WHERE order_items.nama_produk IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('nama_produk');
        });
    }
};
