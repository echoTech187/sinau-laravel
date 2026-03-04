<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // UserSeeder::class,
            RbacSeeder::class,
            MasterDataSeeder::class,
            BusClassSeeder::class,
            SeatLayoutSeeder::class,
            PoBusSeeder::class,
            AgentLocationSeeder::class,
            CrewSeeder::class,
            BusSeeder::class,
        ]);
    }
}
