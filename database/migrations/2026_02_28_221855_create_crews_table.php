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
        Schema::create('crews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crew_position_id')->constrained('crew_positions');
            $table->string('employee_number')->unique();
            $table->string('name');
            $table->string('phone_number');
            $table->string('license_number')->nullable();
            $table->date('license_expired_at')->nullable();
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
        Schema::dropIfExists('crews');
    }
};
