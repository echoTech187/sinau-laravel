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
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['bus_id']);
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('bus_id')->nullable()->change();
            $table->foreign('bus_id')->references('id')->on('buses')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['bus_id']);
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('bus_id')->nullable(false)->change();
            $table->foreign('bus_id')->references('id')->on('buses');
        });
    }
};
