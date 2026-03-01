<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\LocationRole;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Location Roles
        $roles = [
            'Agen',
            'Terminal',
            'Pool / Garasi',
            'Checkpoint',
            'Kantor Cabang',
            'Kantor Pusat',
            'Rest Area',
        ];

        foreach ($roles as $role) {
            LocationRole::updateOrCreate(['name' => $role]);
        }

        // 2. Seed Facilities
        $facilities = [
            ['name' => 'AC', 'icon' => 'heroicon-o-variable'],
            ['name' => 'Toilet', 'icon' => 'heroicon-o-sparkles'],
            ['name' => 'WiFi', 'icon' => 'heroicon-o-wifi'],
            ['name' => 'USB Charger', 'icon' => 'heroicon-o-battery-50'],
            ['name' => 'Reclining Seat', 'icon' => 'heroicon-o-chevron-double-down'],
            ['name' => 'Leg Rest', 'icon' => 'heroicon-o-minus'],
            ['name' => 'Selimut', 'icon' => 'heroicon-o-squares-2x2'],
            ['name' => 'Bantal', 'icon' => 'heroicon-o-swatch'],
            ['name' => 'Snack', 'icon' => 'heroicon-o-shopping-bag'],
            ['name' => 'Makan', 'icon' => 'heroicon-o-cake'],
            ['name' => 'AVOD / TV', 'icon' => 'heroicon-o-tv'],
            ['name' => 'Baggage', 'icon' => 'heroicon-o-briefcase'],
        ];

        foreach ($facilities as $facility) {
            Facility::updateOrCreate(['name' => $facility['name']], $facility);
        }
    }
}
