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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes');
            $table->foreignId('bus_id')->constrained('buses');
            $table->date('departure_date');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_estimate');
            $table->decimal('base_price', 15, 2);
            $table->integer('start_odometer')->nullable();
            $table->integer('end_odometer')->nullable();
            $table->string('trip_type');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
