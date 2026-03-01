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
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained('buses');
            $table->foreignId('maintenance_rule_id')->nullable()->constrained('maintenance_rules');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('reported_by_id');
            $table->text('issue_description');
            $table->string('type');
            $table->string('status');
            $table->integer('odometer_at_service')->nullable();
            $table->date('next_estimated_date')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
