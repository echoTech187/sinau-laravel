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
        Schema::table('crews', function (Blueprint $table) {
            $table->string('nik')->nullable()->unique()->after('id');
            $table->string('gender')->nullable()->after('name');
            $table->date('birth_date')->nullable()->after('gender');
            $table->string('religion')->nullable()->after('birth_date');
            $table->string('marital_status')->nullable()->after('religion');
            $table->string('blood_type')->nullable()->after('marital_status');
            $table->text('original_address')->nullable()->after('blood_type');
            $table->text('current_address')->nullable()->after('original_address');
            $table->string('domicile_city')->nullable()->after('current_address');
            $table->string('contact_phone_1')->nullable()->after('domicile_city');
            $table->string('contact_phone_2')->nullable()->after('contact_phone_1');
            $table->string('rank')->nullable()->after('contact_phone_2');
            $table->string('spouse_name')->nullable()->after('rank');
            $table->integer('children_count')->default(0)->after('spouse_name');
            $table->date('join_date')->nullable()->after('children_count');
            $table->string('education')->nullable()->after('join_date');
            $table->string('region')->nullable()->after('education');
            
            $table->foreignId('pool_id')->nullable()->after('region')->constrained('locations');
            $table->foreignId('agent_id')->nullable()->after('pool_id')->constrained('agents');
            $table->foreignId('bus_id')->nullable()->after('agent_id')->constrained('buses');
            $table->foreignId('route_id')->nullable()->after('bus_id')->constrained('routes');
            
            $table->string('photo_path')->nullable()->after('route_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crews', function (Blueprint $table) {
            $table->dropForeign(['pool_id']);
            $table->dropForeign(['agent_id']);
            $table->dropForeign(['bus_id']);
            $table->dropForeign(['route_id']);
            
            $table->dropColumn([
                'nik', 'gender', 'birth_date', 'religion', 'marital_status', 
                'blood_type', 'original_address', 'current_address', 'domicile_city',
                'contact_phone_1', 'contact_phone_2', 'rank', 'spouse_name', 
                'children_count', 'join_date', 'education', 'region', 
                'pool_id', 'agent_id', 'bus_id', 'route_id', 'photo_path'
            ]);
        });
    }
};
