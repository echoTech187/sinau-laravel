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
        Schema::create('booking_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('booking_id')->constrained('bookings');
            $table->string('seat_number');
            $table->string('passenger_name');
            $table->decimal('ticket_price', 15, 2);
            $table->foreignId('last_scanned_location_id')->nullable()->constrained('locations');
            $table->dateTime('last_scanned_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_tickets');
    }
};
