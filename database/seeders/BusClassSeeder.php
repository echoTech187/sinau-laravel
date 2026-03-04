<?php

namespace Database\Seeders;

use App\Models\BusClass;
use App\Models\Facility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusClassSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('bus_class_facility')->truncate();
        BusClass::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $facilities = Facility::all()->pluck('id', 'name');

        $classes = [
            [
                'name' => 'First Class Sleeper',
                'free_baggage_kg' => 30,
                'description' => 'Cabin private dengan kursi rebah 150 derajat.',
                'facilities' => ['AC', 'Toilet', 'Snack', 'Makan', 'Sleeper Seat', 'WiFi', 'USB Charger', 'AVOD / TV']
            ],
            [
                'name' => 'Super Top',
                'free_baggage_kg' => 25,
                'description' => 'Kelas Premium 1-2 dengan kursi luas dan legrest.',
                'facilities' => ['AC', 'Toilet', 'Snack', 'Makan', 'Reclining Seat', 'Leg Rest', 'WiFi', 'USB Charger', 'AVOD / TV']
            ],
            [
                'name' => 'Executive Plus',
                'free_baggage_kg' => 20,
                'description' => 'Kelas Menengah 2-2 yang nyaman dan lega.',
                'facilities' => ['AC', 'Toilet', 'Snack', 'Makan', 'Reclining Seat', 'Leg Rest', 'WiFi', 'USB Charger']
            ],
            [
                'name' => 'VIP Class',
                'free_baggage_kg' => 15,
                'description' => 'Kelas Standar 2-2 dengan fasilitas memadai.',
                'facilities' => ['AC', 'Toilet', 'Snack', 'Reclining Seat']
            ],
            [
                'name' => 'Travel Hiace',
                'free_baggage_kg' => 10,
                'description' => 'Kelas Travel 1-2 menggunakan armada Toyota Hiace.',
                'facilities' => ['AC', 'Snack', 'Reclining Seat']
            ]
        ];

        foreach ($classes as $classData) {
            $classFacilities = $classData['facilities'];
            unset($classData['facilities']);

            $busClass = BusClass::create($classData);

            $facilityIds = [];
            foreach ($classFacilities as $fName) {
                if (isset($facilities[$fName])) {
                    $facilityIds[] = $facilities[$fName];
                }
            }
            $busClass->facilities()->attach($facilityIds);
        }
    }
}
