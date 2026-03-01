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
        Schema::create('manifest_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('manifest_id')->constrained('operational_manifests');
            $table->foreignId('inspection_item_id')->constrained('inspection_items');
            $table->unsignedBigInteger('checked_by_id');
            $table->decimal('earned_score', 8, 2);
            $table->text('notes')->nullable();
            $table->string('result');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manifest_checklists');
    }
};
