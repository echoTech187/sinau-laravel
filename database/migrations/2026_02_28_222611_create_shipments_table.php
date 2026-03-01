<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('waybill_number')->unique();
            $table->string('barcode_number')->unique();
            $table->foreignId('schedule_id')->nullable()->constrained('schedules');
            $table->foreignUuid('booking_id')->nullable()->constrained('bookings');
            $table->foreignId('origin_location_id')->constrained('locations');
            $table->foreignId('destination_location_id')->constrained('locations');
            $table->string('sender_name');
            $table->string('sender_phone');
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('item_description');
            $table->decimal('actual_weight_kg', 10, 2);
            $table->decimal('chargeable_weight_kg', 10, 2);
            $table->decimal('shipping_cost', 15, 2);
            $table->unsignedBigInteger('created_by_user_id');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
