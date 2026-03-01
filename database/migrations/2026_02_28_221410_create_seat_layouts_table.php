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
        Schema::create('seat_layouts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('grid_rows');
            $table->integer('grid_columns');
            $table->json('layout_mapping');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_layouts');
    }
};
