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
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('booking_code')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->foreignId('schedule_id')->constrained('schedules');
            $table->foreignId('boarding_location_id')->constrained('locations');
            $table->foreignId('dropoff_location_id')->constrained('locations');
            $table->foreignId('agent_id')->nullable()->constrained('agents');
            $table->integer('total_seats');
            $table->decimal('total_amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->dateTime('expired_at');
            $table->string('payment_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
