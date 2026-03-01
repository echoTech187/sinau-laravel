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
        Schema::create('inspection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('inspection_categories');
            $table->string('item_name');
            $table->integer('max_score');
            $table->boolean('is_critical')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_items');
    }
};
