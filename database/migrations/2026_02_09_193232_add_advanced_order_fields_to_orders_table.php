<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan:
     * - payment_deadline: Batas waktu pembayaran (untuk auto-cancel)
     * - rejection_reason: Alasan penolakan bukti bayar
     * - courier_name: Nama kurir/pengantar
     * - courier_phone: Nomor HP kurir
     * - tracking_number: Nomor resi (opsional)
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Deadline pembayaran (default 24 jam dari order dibuat)
            $table->timestamp('payment_deadline')->nullable()->after('payment_uploaded_at');
            
            // Alasan penolakan dari admin
            $table->text('rejection_reason')->nullable()->after('payment_deadline');
            
            // Info kurir/pengantar
            $table->string('courier_name')->nullable()->after('delivery_time');
            $table->string('courier_phone')->nullable()->after('courier_name');
            $table->string('tracking_number')->nullable()->after('courier_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_deadline',
                'rejection_reason',
                'courier_name',
                'courier_phone',
                'tracking_number',
            ]);
        });
    }
};
