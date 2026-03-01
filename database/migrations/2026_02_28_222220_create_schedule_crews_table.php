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
        Schema::create('schedule_crews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules');
            $table->foreignId('crew_id')->constrained('crews');
            $table->foreignId('assigned_position_id')->constrained('crew_positions');
            $table->foreignId('boarding_location_id')->constrained('locations');
            $table->foreignId('dropoff_location_id')->constrained('locations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_crews');
    }
};
