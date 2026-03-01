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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('agent_code')->unique();
            $table->foreignId('location_id')->nullable()->constrained('locations');
            $table->foreignId('parent_branch_id')->nullable()->constrained('agents');
            $table->string('name');
            $table->string('phone_number');
            $table->string('type');
            $table->string('commission_type');
            $table->decimal('commission_value', 10, 2);
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
        Schema::dropIfExists('agents');
    }
};
