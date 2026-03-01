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
        Schema::create('operational_manifests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('schedule_id')->constrained('schedules');
            $table->string('manifest_number')->unique();
            $table->dateTime('issued_at')->nullable();
            $table->unsignedBigInteger('authorized_by_id');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_manifests');
    }
};
