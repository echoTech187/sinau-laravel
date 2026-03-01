<?php

namespace Database\Seeders;

use App\Enums\AgentStatus;
use App\Enums\AgentType;
use App\Enums\BusStatus;
use App\Enums\CommissionType;
use App\Enums\CrewStatus;
use App\Enums\GeofenceType;
use App\Enums\InventoryStatus;
use App\Enums\ScheduleStatus;
use App\Enums\StopType;
use App\Enums\TripType;
use App\Models\Agent;
use App\Models\AgentBalance;
use App\Models\Bus;
use App\Models\BusClass;
use App\Models\BusInventory;
use App\Models\Crew;
use App\Models\CrewPosition;
use App\Models\Facility;
use App\Models\InspectionCategory;
use App\Models\InspectionItem;
use App\Models\Location;
use App\Models\LocationRole;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Schedule;
use App\Models\ScheduleCrew;
use App\Models\SeatLayout;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PoBusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ---------------------------------------------------------
        // CLEANUP
        // ---------------------------------------------------------
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('booking_tickets')->truncate();
        DB::table('bookings')->truncate();
        DB::table('schedule_crews')->truncate();
        DB::table('schedules')->truncate();
        DB::table('route_stops')->truncate();
        DB::table('routes')->truncate();

        DB::table('agent_balances')->truncate();
        DB::table('agents')->truncate();
        DB::table('location_has_role')->truncate();
        DB::table('location_roles')->truncate();
        DB::table('locations')->truncate();

        DB::table('crews')->truncate();
        DB::table('crew_positions')->truncate();

        DB::table('bus_inventories')->truncate();
        DB::table('buses')->truncate();
        DB::table('bus_class_facility')->truncate();
        DB::table('facilities')->truncate();
        DB::table('seat_layouts')->truncate();
        DB::table('bus_classes')->truncate();

        DB::table('inspection_items')->truncate();
        DB::table('inspection_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ---------------------------------------------------------
        // 1. MASTER DATA LOKASI & AGEN
        // ---------------------------------------------------------
        $locRoles = [
            'Terminal' => LocationRole::create(['name' => 'Terminal']),
            'Pool' => LocationRole::create(['name' => 'Pool / Garasi']),
            'Agen' => LocationRole::create(['name' => 'Agen Penjualan']),
            'Rest Area' => LocationRole::create(['name' => 'Rest Area / Rumah Makan']),
        ];

        $locations = [
            'Pondok Pinang' => Location::create([
                'name' => 'Terminal Pondok Pinang', 'city' => 'Jakarta Selatan', 'province' => 'DKI Jakarta',
                'address' => 'Jl. Ciputat Raya', 'latitude' => -6.2828, 'longitude' => 106.7770,
                'geofence_type' => GeofenceType::CIRCULAR, 'geofence_radius_meter' => 150,
                'has_maintenance_facility' => false,
            ]),
            'Pulo Gebang' => Location::create([
                'name' => 'Terminal Terpadu Pulo Gebang', 'city' => 'Jakarta Timur', 'province' => 'DKI Jakarta',
                'address' => 'Jl. Pulo Gebang', 'latitude' => -6.2104, 'longitude' => 106.9532,
                'geofence_type' => GeofenceType::POLYGON, 'geofence_polygon_path' => '[[-6.210,-106.951], [-6.211,-106.955], [-6.215,-106.955]]',
                'has_maintenance_facility' => false,
            ]),
            'Garasi Bitung' => Location::create([
                'name' => 'Pool Bitung (Kantor Pusat)', 'city' => 'Tangerang', 'province' => 'Banten',
                'address' => 'Jl. Raya Serang Km 10', 'latitude' => -6.2230, 'longitude' => 106.5510,
                'geofence_type' => GeofenceType::POLYGON, 'geofence_polygon_path' => '[[-6.22,-106.55], [-6.23,-106.55], [-6.23,-106.56]]',
                'has_maintenance_facility' => true,
            ]),
            'RM Subang' => Location::create([
                'name' => 'Rest Area KM 102 Subang', 'city' => 'Subang', 'province' => 'Jawa Barat',
                'address' => 'Tol Cipali KM 102', 'latitude' => -6.4957, 'longitude' => 107.6534,
                'geofence_type' => GeofenceType::CIRCULAR, 'geofence_radius_meter' => 200,
                'has_maintenance_facility' => true, // Ada tim P2H darurat
            ]),
            'Terminal Tirtonadi' => Location::create([
                'name' => 'Terminal Tirtonadi', 'city' => 'Surakarta', 'province' => 'Jawa Tengah',
                'address' => 'Jl. Jend. A. Yani', 'latitude' => -7.5540, 'longitude' => 110.8202,
                'geofence_type' => GeofenceType::CIRCULAR, 'geofence_radius_meter' => 100,
                'has_maintenance_facility' => false,
            ]),
        ];

        // Attach Roles to Locations
        $locations['Pondok Pinang']->roles()->attach([$locRoles['Terminal']->id, $locRoles['Agen']->id]);
        $locations['Pulo Gebang']->roles()->attach([$locRoles['Terminal']->id, $locRoles['Agen']->id]);
        $locations['Garasi Bitung']->roles()->attach([$locRoles['Pool']->id, $locRoles['Terminal']->id]);
        $locations['RM Subang']->roles()->attach([$locRoles['Rest Area']->id, $locRoles['Pool']->id]); // Pool karena punya fasilitas maintenance
        $locations['Terminal Tirtonadi']->roles()->attach([$locRoles['Terminal']->id, $locRoles['Agen']->id]);

        $agents = [
            Agent::create([
                'agent_code' => 'AGT-PNP-01', 'location_id' => $locations['Pondok Pinang']->id,
                'name' => 'Agen Resmi Pondok Pinang', 'phone_number' => '081234567890',
                'type' => AgentType::BRANCH_OFFICE, 'commission_type' => CommissionType::FLAT,
                'commission_value' => 0, 'status' => AgentStatus::ACTIVE,
            ]),
            Agent::create([
                'agent_code' => 'AGT-TRT-01', 'location_id' => $locations['Terminal Tirtonadi']->id,
                'name' => 'Mitra Tirtonadi Mantap', 'phone_number' => '089876543210',
                'type' => AgentType::PARTNER_EXCLUSIVE, 'commission_type' => CommissionType::PERCENTAGE,
                'commission_value' => 10.00, 'status' => AgentStatus::ACTIVE,
            ]),
        ];

        foreach ($agents as $agt) {
            AgentBalance::create([
                'agent_id' => $agt->id,
                'current_balance' => 5000000,
                'credit_limit' => $agt->type === AgentType::BRANCH_OFFICE ? 100000000 : 0,
            ]);
        }

        // ---------------------------------------------------------
        // 2. MASTER DATA ARMADA & KELAS
        // ---------------------------------------------------------
        $fAc = Facility::create(['name' => 'AC', 'icon' => 'heroicon-o-variable']);
        $fToilet = Facility::create(['name' => 'Toilet', 'icon' => 'heroicon-o-sparkles']);
        $fSnack = Facility::create(['name' => 'Snack & Air Mineral', 'icon' => 'heroicon-o-shopping-bag']);
        $fMeal = Facility::create(['name' => 'Servis Makan 1x', 'icon' => 'heroicon-o-cake']);
        $fReclining = Facility::create(['name' => 'Reclining Seat 2-2', 'icon' => 'heroicon-o-chevron-double-down']);
        $fLegrest = Facility::create(['name' => 'Leg Rest', 'icon' => 'heroicon-o-minus']);
        $fSleeper = Facility::create(['name' => 'Sleeper Seat', 'icon' => 'heroicon-o-moon']);

        $classVip = BusClass::create(['name' => 'VIP Class', 'free_baggage_kg' => 15, 'description' => 'Kelas menengah yang nyaman dengan kursi 2-2.']);
        $classVip->facilities()->attach([$fAc->id, $fToilet->id, $fSnack->id, $fReclining->id]);

        $classExec = BusClass::create(['name' => 'Executive Plus', 'free_baggage_kg' => 20, 'description' => 'Kelas Premium dengan kursi 2-2 yang lega dan legrest.']);
        $classExec->facilities()->attach([$fAc->id, $fToilet->id, $fSnack->id, $fMeal->id, $fReclining->id, $fLegrest->id]);

        $classSleeper = BusClass::create(['name' => 'First Class Sleeper', 'free_baggage_kg' => 25, 'description' => 'Cabin private dengan kursi rebah 150 derajat.']);
        $classSleeper->facilities()->attach([$fAc->id, $fToilet->id, $fSnack->id, $fMeal->id, $fSleeper->id]);

        // Layout JSON Examples
        $layoutVip = SeatLayout::create([
            'name' => 'VIP 36 Seats (2-2 Toilet Belakang)',
            'grid_rows' => 10,
            'grid_columns' => 5,
            'layout_mapping' => [
                ['row' => 1, 'col' => 1, 'type' => 'seat', 'seat_number' => '1A'],
                ['row' => 1, 'col' => 2, 'type' => 'seat', 'seat_number' => '1B'],
                ['row' => 1, 'col' => 3, 'type' => 'aisle', 'label' => ''],
                ['row' => 1, 'col' => 4, 'type' => 'seat', 'seat_number' => '1C'],
                ['row' => 1, 'col' => 5, 'type' => 'seat', 'seat_number' => '1D'],
                ['row' => 10, 'col' => 5, 'type' => 'toilet', 'label' => ''],
                ['row' => 10, 'col' => 4, 'type' => 'door', 'label' => ''],
            ],
        ]);

        $buses = [
            Bus::create([
                'bus_class_id' => $classExec->id, 'seat_layout_id' => $layoutVip->id, 'base_pool_id' => $locations['Garasi Bitung']->id,
                'fleet_code' => 'EX-001', 'plate_number' => 'B 7123 KGA', 'rfid_tag_id' => '1A2B3C4D',
                'name' => 'Sapu Jagat', 'chassis_brand' => 'Mercedes-Benz', 'chassis_type' => 'OH 1626 L',
                'body_maker' => 'Adiputro', 'body_model' => 'Jetbus 5 MHD', 'manufacture_year' => 2023,
                'engine_number' => 'OMB123456', 'chassis_number' => 'MHL123456789',
                'total_seats' => 32, 'max_baggage_weight_kg' => 1500, 'max_baggage_volume_m3' => 10,
                'stnk_expired_at' => Carbon::now()->addMonths(10), 'kir_expired_at' => Carbon::now()->addMonths(4),
                'kps_expired_at' => Carbon::now()->addYears(2), 'insurance_expired_at' => Carbon::now()->addMonths(11),
                'current_odometer' => 125000, 'average_daily_km' => 600, 'status' => BusStatus::ACTIVE,
            ]),
            Bus::create([
                'bus_class_id' => $classVip->id, 'seat_layout_id' => $layoutVip->id, 'base_pool_id' => $locations['Garasi Bitung']->id,
                'fleet_code' => 'VP-015', 'plate_number' => 'B 7555 KGB', 'rfid_tag_id' => '1A2B3C5E',
                'name' => 'Bima Sena', 'chassis_brand' => 'Hino', 'chassis_type' => 'RK8 R260',
                'body_maker' => 'Laksana', 'body_model' => 'Legacy SR3', 'manufacture_year' => 2021,
                'engine_number' => 'J08E12345', 'chassis_number' => 'HNK123456789',
                'total_seats' => 36, 'max_baggage_weight_kg' => 1200, 'max_baggage_volume_m3' => 8,
                'stnk_expired_at' => Carbon::now()->addMonths(2), 'kir_expired_at' => Carbon::now()->addMonths(1),
                'kps_expired_at' => Carbon::now()->addYears(1), 'insurance_expired_at' => Carbon::now()->addMonths(5),
                'current_odometer' => 350000, 'average_daily_km' => 550, 'status' => BusStatus::ACTIVE,
            ]),
        ];

        BusInventory::create([
            'bus_id' => $buses[0]->id, 'item_name' => 'Dongkrak Buaya 20T', 'serial_number' => 'DK-20-001', 'status' => InventoryStatus::AVAILABLE,
        ]);

        // ---------------------------------------------------------
        // 3. MASTER DATA KRU
        // ---------------------------------------------------------
        $posDriver = CrewPosition::create(['name' => 'Kapten / Driver 1', 'base_allowance' => 250000]);
        $posCoDriver = CrewPosition::create(['name' => 'Driver 2', 'base_allowance' => 200000]);
        $posAssistant = CrewPosition::create(['name' => 'Kernet/Pramugara', 'base_allowance' => 150000]);

        $crews = [
            Crew::create([
                'crew_position_id' => $posDriver->id, 'employee_number' => 'CRW-1001',
                'name' => 'Budi Santoso', 'phone_number' => '081122334455',
                'license_number' => 'BII-UMUM-123', 'license_expired_at' => Carbon::now()->addYears(2),
                'status' => CrewStatus::ACTIVE,
            ]),
            Crew::create([
                'crew_position_id' => $posCoDriver->id, 'employee_number' => 'CRW-1002',
                'name' => 'Agus Salim', 'phone_number' => '082233445566',
                'license_number' => 'BII-UMUM-456', 'license_expired_at' => Carbon::now()->addMonths(6),
                'status' => CrewStatus::ACTIVE,
            ]),
            Crew::create([
                'crew_position_id' => $posAssistant->id, 'employee_number' => 'CRW-1003',
                'name' => 'Eko Prasetyo', 'phone_number' => '083344556677',
                'status' => CrewStatus::ACTIVE,
            ]),
        ];

        // ---------------------------------------------------------
        // 4. OPERASIONAL RUTE, STOP, JADWAL
        // ---------------------------------------------------------
        $route = Route::create([
            'route_code' => 'JKT-SLO-01', 'name' => 'Jakarta - Solo (Via Tol TransJawa)',
            'origin_location_id' => $locations['Pondok Pinang']->id,
            'destination_location_id' => $locations['Terminal Tirtonadi']->id,
            'distance_km' => 540,
        ]);

        RouteStop::insert([
            ['route_id' => $route->id, 'location_id' => $locations['Pondok Pinang']->id, 'stop_order' => 1, 'type' => StopType::BOARDING_ONLY, 'is_checkpoint' => true],
            ['route_id' => $route->id, 'location_id' => $locations['Pulo Gebang']->id, 'stop_order' => 2, 'type' => StopType::BOARDING_ONLY, 'is_checkpoint' => true],
            ['route_id' => $route->id, 'location_id' => $locations['RM Subang']->id, 'stop_order' => 3, 'type' => StopType::TRANSIT, 'is_checkpoint' => true],
            ['route_id' => $route->id, 'location_id' => $locations['Terminal Tirtonadi']->id, 'stop_order' => 4, 'type' => StopType::DROPOFF_ONLY, 'is_checkpoint' => true],
        ]);

        $schedule = Schedule::create([
            'route_id' => $route->id, 'bus_id' => $buses[0]->id,
            'departure_date' => Carbon::tomorrow()->toDateString(),
            'departure_time' => '15:00:00',
            'arrival_estimate' => Carbon::tomorrow()->addHours(10)->toDateTimeString(),
            'base_price' => 350000,
            'start_odometer' => null, 'end_odometer' => null,
            'trip_type' => TripType::REVENUE, 'status' => ScheduleStatus::SCHEDULED,
        ]);

        ScheduleCrew::insert([
            ['schedule_id' => $schedule->id, 'crew_id' => $crews[0]->id, 'assigned_position_id' => $posDriver->id, 'boarding_location_id' => $locations['Pondok Pinang']->id, 'dropoff_location_id' => $locations['Terminal Tirtonadi']->id],
            ['schedule_id' => $schedule->id, 'crew_id' => $crews[1]->id, 'assigned_position_id' => $posCoDriver->id, 'boarding_location_id' => $locations['Pondok Pinang']->id, 'dropoff_location_id' => $locations['Terminal Tirtonadi']->id],
            ['schedule_id' => $schedule->id, 'crew_id' => $crews[2]->id, 'assigned_position_id' => $posAssistant->id, 'boarding_location_id' => $locations['Pondok Pinang']->id, 'dropoff_location_id' => $locations['Terminal Tirtonadi']->id],
        ]);

        // ---------------------------------------------------------
        // 5. MASTER INSPECTION (P2H / SJO)
        // ---------------------------------------------------------
        // Asumsi roles ada di user_has_roles/roles, tapi karena ID-nya integer kita set manual misal ID 6 untuk Mekanik (jika mengacu ke RBAC Warehouse/Maintenance)
        $catMesin = InspectionCategory::create([
            'name' => 'Pemeriksaan Mesin & Cairan (P2H)', 'target_role_id' => 6, // 6 = Warehouse / Mechanic di RBAC
            'min_passing_percentage' => 100,
        ]);

        $catInterior = InspectionCategory::create([
            'name' => 'Fasilitas Penumpang', 'target_role_id' => 3, // 3 = Manager/Checker
            'min_passing_percentage' => 80,
        ]);

        InspectionItem::insert([
            ['category_id' => $catMesin->id, 'item_name' => 'Level Oli Mesin', 'max_score' => 10, 'is_critical' => true],
            ['category_id' => $catMesin->id, 'item_name' => 'Cairan Radiator', 'max_score' => 10, 'is_critical' => true],
            ['category_id' => $catMesin->id, 'item_name' => 'Tekanan Angin Rem', 'max_score' => 20, 'is_critical' => true],
            ['category_id' => $catInterior->id, 'item_name' => 'Kebersihan Toilet', 'max_score' => 10, 'is_critical' => false],
            ['category_id' => $catInterior->id, 'item_name' => 'Fungsi AC Kabin', 'max_score' => 15, 'is_critical' => true],
            ['category_id' => $catInterior->id, 'item_name' => 'Ketersediaan Selimut', 'max_score' => 5, 'is_critical' => false],
        ]);

        $this->command->info('✅ PO Bus Seeder berhasil dijalankan!');
    }
}
