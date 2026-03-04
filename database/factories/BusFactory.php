<?php

namespace Database\Factories;

use App\Enums\BusStatus;
use App\Models\Bus;
use App\Models\BusClass;
use App\Models\Location;
use App\Models\SeatLayout;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bus>
 */
class BusFactory extends Factory
{
    protected $model = Bus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $chassisOptions = [
            ['brand' => 'Mercedes-Benz', 'type' => 'OH 1626 L'],
            ['brand' => 'Mercedes-Benz', 'type' => 'O 500 RS 1836'],
            ['brand' => 'Hino', 'type' => 'RK8 R260'],
            ['brand' => 'Hino', 'type' => 'RN 285'],
            ['brand' => 'Scania', 'type' => 'K360IB'],
            ['brand' => 'Scania', 'type' => 'K410IB'],
            ['brand' => 'Volvo', 'type' => 'B11R'],
        ];

        $bodyOptions = [
            ['maker' => 'Adiputro', 'model' => 'Jetbus 5 MHD'],
            ['maker' => 'Adiputro', 'model' => 'Jetbus 3+ SHD'],
            ['maker' => 'Laksana', 'model' => 'Legacy SR3'],
            ['maker' => 'Laksana', 'model' => 'Legacy SR2 HD Prime'],
            ['maker' => 'Tentrem', 'model' => 'Avante H8'],
            ['maker' => 'New Armada', 'model' => 'Skylander R22'],
        ];

        $chassis = $this->faker->randomElement($chassisOptions);
        $body = $this->faker->randomElement($bodyOptions);

        $busNames = [
            'Sapu Jagat', 'Bima Sena', 'Arjun Wijaya', 'Sakti Mandraguna', 'Wira Brata',
            'Gatotkaca', 'Nakula Sadewa', 'Pandawa Lima', 'Kresna Duta', 'Bima Sakti',
            'Suryakencana', 'Cakra Bhirawa', 'Satria Muda', 'Pangeran Jayakarta',
            'Jayakatwang', 'Singasari', 'Majapahit', 'Sriwijaya', 'Padjadjaran',
            'Mataram', 'Kalingga', 'Galuh', 'Tarumanegara', 'Kutai Martapura',
            'Singosari', 'Kertanegara', 'Ken Arok', 'Raden Wijaya', 'Gajah Mada',
            'Hayam Wuruk', 'Tribhuwana', 'Adityawarman', 'Mulawarman', 'Purnawarman',
            'Sanjaya', 'Syailendra', 'Samaratungga', 'Balaputradewa', 'Airlangga',
            'Jayabaya', 'Jayanegara', 'Gayatri', 'Ken Dedes', 'Anusapati',
            'Tohjaya', 'Ranggawuni', 'Mahesa Cempaka', 'Wisnuwardhana', 'Raden Patah',
            'Sultan Trenggono', 'Sultan Ageng', 'Sultan Hasanuddin', 'Sultan Agung'
        ];

        return [
            'bus_class_id' => BusClass::inRandomOrder()->first()?->id ?? 1,
            'seat_layout_id' => SeatLayout::inRandomOrder()->first()?->id ?? 1,
            'base_pool_id' => Location::whereHas('roles', function($q) {
                $q->whereIn('name', ['Pool / Garasi', 'Kantor Cabang']);
            })->inRandomOrder()->first()?->id ?? 3,
            'fleet_code' => 'BUS-' . $this->faker->unique()->numberBetween(100, 999),
            'plate_number' => 'B ' . $this->faker->unique()->numberBetween(1000, 9999) . ' ' . strtoupper($this->faker->unique()->lexify('???')),
            'rfid_tag_id' => strtoupper($this->faker->unique()->bothify('########')),
            'name' => $this->faker->unique()->randomElement($busNames),
            'chassis_brand' => $chassis['brand'],
            'chassis_type' => $chassis['type'],
            'body_maker' => $body['maker'],
            'body_model' => $body['model'],
            'manufacture_year' => $this->faker->numberBetween(2018, 2024),
            'engine_number' => strtoupper($this->faker->unique()->bothify('???######')),
            'chassis_number' => strtoupper($this->faker->unique()->bothify('???#########')),
            'total_seats' => $this->faker->randomElement([30, 32, 36, 40]),
            'max_baggage_weight_kg' => $this->faker->numberBetween(1000, 2500),
            'max_baggage_volume_m3' => $this->faker->randomFloat(2, 5, 20),
            'stnk_expired_at' => $this->faker->dateTimeBetween('now', '+2 years'),
            'kir_expired_at' => $this->faker->dateTimeBetween('now', '+6 months'),
            'kps_expired_at' => $this->faker->dateTimeBetween('now', '+3 years'),
            'insurance_expired_at' => $this->faker->dateTimeBetween('now', '+1 year'),
            'current_odometer' => $this->faker->numberBetween(10000, 500000),
            'average_daily_km' => $this->faker->numberBetween(300, 800),
            'status' => $this->faker->randomElement(BusStatus::cases()),
        ];
    }
}
