<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_operational_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('agents')->cascadeOnDelete();
            $table->tinyInteger('day')->comment('0=Senin, 1=Selasa, 2=Rabu, 3=Kamis, 4=Jumat, 5=Sabtu, 6=Minggu');
            $table->time('open_time')->nullable()->comment('null = tutup pada hari ini');
            $table->time('close_time')->nullable()->comment('null = tutup pada hari ini');
            $table->boolean('is_24_hours')->default(false);
            $table->string('notes')->nullable()->comment('catatan khusus jam operasional');
            $table->timestamps();

            $table->unique(['agent_id', 'day'], 'agent_day_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_operational_hours');
    }
};
