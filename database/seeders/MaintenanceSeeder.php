<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            [
                'task_name' => 'Ganti Oli Mesin',
                'interval_km' => 10000,
                'tolerance_km' => 1000,
                'estimated_hours' => 3,
            ],
            [
                'task_name' => 'Servis Besar (Tune Up)',
                'interval_km' => 50000,
                'tolerance_km' => 2000,
                'estimated_hours' => 8,
            ],
            [
                'task_name' => 'Rotasi & Cek Ban',
                'interval_km' => 20000,
                'tolerance_km' => 1000,
                'estimated_hours' => 2,
            ],
            [
                'task_name' => 'Ganti Filter Udara',
                'interval_km' => 15000,
                'tolerance_km' => 500,
                'estimated_hours' => 1,
            ],
        ];

        foreach ($rules as $rule) {
            \App\Models\MaintenanceRule::updateOrCreate(
                ['task_name' => $rule['task_name']],
                $rule
            );
        }
    }
}
