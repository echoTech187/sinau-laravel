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
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->string('chassis_brand')->nullable(); // Mercedes-Benz, Scania, Hino (null = general)
            $table->integer('km_interval')->nullable(); // E.g., 20000, 60000
            $table->string('name'); // e.g., "Paket Servis 60.000 KM"
            $table->boolean('is_major')->default(false); // Penanda servis besar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_packages');
    }
};
