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
        Schema::create('manifest_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('manifest_id')->constrained('operational_manifests');
            $table->foreignId('category_id')->constrained('inspection_categories');
            $table->unsignedBigInteger('approved_by_id');
            $table->decimal('achieved_percentage', 5, 2);
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manifest_approvals');
    }
};
