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
            $table->index('status');
            $table->index('departure_date');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index('payment_status');
        });

        Schema::table('operational_manifests', function (Blueprint $table) {
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['departure_date']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
        });

        Schema::table('operational_manifests', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
