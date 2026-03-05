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
        Schema::table('maintenance_rules', function (Blueprint $table) {
            $table->integer('estimated_hours')->default(2)->after('tolerance_km');
            $table->foreignId('preferred_agent_id')->nullable()->after('estimated_hours')->constrained('agents')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_rules', function (Blueprint $table) {
            $table->dropForeign(['preferred_agent_id']);
            $table->dropColumn(['estimated_hours', 'preferred_agent_id']);
        });
    }
};
