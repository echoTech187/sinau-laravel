<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add new agent_id column
        if (!Schema::hasColumn('route_stops', 'agent_id')) {
            Schema::table('route_stops', function (Blueprint $table) {
                $table->foreignId('agent_id')->nullable()->after('route_id')->constrained('agents')->nullOnDelete();
            });
        }

        // Step 2: Drop old location_id FK and column
        if (Schema::hasColumn('route_stops', 'location_id')) {
            DB::statement('ALTER TABLE route_stops DROP FOREIGN KEY route_stops_location_id_foreign');
            Schema::table('route_stops', function (Blueprint $table) {
                $table->dropColumn('location_id');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('route_stops', 'location_id')) {
            Schema::table('route_stops', function (Blueprint $table) {
                $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            });
        }
        if (Schema::hasColumn('route_stops', 'agent_id')) {
            Schema::table('route_stops', function (Blueprint $table) {
                $table->dropForeign(['agent_id']);
                $table->dropColumn('agent_id');
            });
        }
    }
};

