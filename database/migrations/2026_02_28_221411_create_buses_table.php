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
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_class_id')->constrained('bus_classes');
            $table->foreignId('seat_layout_id')->constrained('seat_layouts');
            $table->unsignedBigInteger('base_pool_id');
            $table->string('fleet_code')->unique();
            $table->string('plate_number')->unique();
            $table->string('rfid_tag_id')->unique()->nullable();
            $table->string('name')->nullable();
            $table->string('chassis_brand');
            $table->string('chassis_type');
            $table->string('body_maker');
            $table->string('body_model');
            $table->integer('manufacture_year');
            $table->string('engine_number')->unique();
            $table->string('chassis_number')->unique();
            $table->integer('total_seats');
            $table->integer('max_baggage_weight_kg');
            $table->decimal('max_baggage_volume_m3', 8, 2)->nullable();
            $table->date('stnk_expired_at');
            $table->date('kir_expired_at');
            $table->date('kps_expired_at');
            $table->date('insurance_expired_at');
            $table->integer('current_odometer');
            $table->integer('average_daily_km');
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
        Schema::dropIfExists('buses');
    }
};
