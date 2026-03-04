<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\BusClass;
use App\Models\Location;
use App\Models\SeatLayout;
use Illuminate\Database\Seeder;

class BusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Bus::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $busClasses = BusClass::all();
        $seatLayouts = SeatLayout::all();
        $pools = Location::whereHas('roles', function($q) {
            $q->whereIn('name', ['Pool / Garasi', 'Kantor Cabang']);
        })->get();

        if ($busClasses->isEmpty() || $seatLayouts->isEmpty() || $pools->isEmpty()) {
            $this->command->error('Pastikan Master Data (Kelas Bus, Layout Kursi, dan Lokasi Pool) sudah terisi sebelum menjalankan seeder ini.');
            return;
        }

        $this->command->info('Menghasilkan 25 armada bus baru...');

        // Kita buat mapping untuk prefix kode lambung (fleet_code) berdasarkan kelas
        $classPrefixes = [
            'Executive Plus' => 'EX',
            'VIP Class' => 'VP',
            'First Class Sleeper' => 'SL',
        ];

        for ($i = 1; $i <= 25; $i++) {
            $class = $busClasses->random();
            $prefix = $classPrefixes[$class->name] ?? 'BUS';
            
            Bus::factory()->create([
                'bus_class_id' => $class->id,
                'seat_layout_id' => $seatLayouts->random()->id,
                'base_pool_id' => $pools->random()->id,
                'fleet_code' => $prefix . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
            ]);
        }

        $this->command->info('✅ Berhasil membuat 25 armada bus!');
    }
}
