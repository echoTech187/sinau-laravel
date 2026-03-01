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
        Schema::create('maintenance_rules', function (Blueprint $table) {
            $table->id();
            $table->string('task_name');
            $table->string('chassis_brand')->nullable();
            $table->integer('interval_km');
            $table->integer('tolerance_km');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_rules');
    }
};
