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
        Schema::create('maintenance_rule_service_package', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maintenance_rule_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            // Unik untuk mencegah duplikasi
            $table->unique(['service_package_id', 'maintenance_rule_id'], 'pkg_rule_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_rule_service_package');
    }
};
