<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_drivers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('no_hp');
            $table->string('kendaraan')->nullable(); // e.g. "Motor Honda Beat - B 1234 XYZ"
            $table->string('zona')->nullable(); // delivery zone
            $table->enum('status', ['active', 'inactive', 'on_delivery'])->default('active');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Link orders to drivers
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('courier_driver_id')->nullable()->after('status')
                  ->constrained('courier_drivers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['courier_driver_id']);
            $table->dropColumn('courier_driver_id');
        });
        Schema::dropIfExists('courier_drivers');
    }
};
