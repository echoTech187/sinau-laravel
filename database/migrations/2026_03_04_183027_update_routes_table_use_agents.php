<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add new agent FK columns (skip if already added from a previous failed run)
        if (!Schema::hasColumn('routes', 'origin_agent_id')) {
            Schema::table('routes', function (Blueprint $table) {
                $table->foreignId('origin_agent_id')->nullable()->after('name')->constrained('agents')->nullOnDelete();
                $table->foreignId('destination_agent_id')->nullable()->after('origin_agent_id')->constrained('agents')->nullOnDelete();
            });
        }

        // Step 2: Drop old location FK columns (skip if already dropped)
        if (Schema::hasColumn('routes', 'origin_location_id')) {
            // Drop FK constraints using raw SQL for reliability across engines/naming conventions
            DB::statement('ALTER TABLE routes DROP FOREIGN KEY routes_origin_location_id_foreign');
            DB::statement('ALTER TABLE routes DROP FOREIGN KEY routes_destination_location_id_foreign');
            Schema::table('routes', function (Blueprint $table) {
                $table->dropColumn(['origin_location_id', 'destination_location_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            if (!Schema::hasColumn('routes', 'origin_location_id')) {
                $table->foreignId('origin_location_id')->nullable()->constrained('locations')->nullOnDelete();
                $table->foreignId('destination_location_id')->nullable()->constrained('locations')->nullOnDelete();
            }
            if (Schema::hasColumn('routes', 'origin_agent_id')) {
                $table->dropForeign(['origin_agent_id']);
                $table->dropForeign(['destination_agent_id']);
                $table->dropColumn(['origin_agent_id', 'destination_agent_id']);
            }
        });
    }
};
