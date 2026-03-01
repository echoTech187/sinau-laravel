<?php

namespace Database\Seeders;

use App\Models\InspectionCategory;
use App\Models\InspectionItem;
use App\Models\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InspectionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        InspectionCategory::truncate();
        InspectionItem::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $roleMechanic = Roles::where('slug', '=', 'administrator', 'and')->first(); // Temporary mapping until specific roles are clarified
        $roleStewardess = Roles::where('slug', '=', 'super-admin', 'and')->first(); 

        // 1. Kategori: Mesin & Kaki-Kaki
        $catMesin = InspectionCategory::create([
            'name' => 'Mesin & Kaki-Kaki',
            'target_role_id' => $roleMechanic->id,
            'min_passing_percentage' => 100,
        ]);

        $itemsMesin = [
            ['item_name' => 'Fungsi Pengereman (Rem Utama)', 'max_score' => 20, 'is_critical' => true],
            ['item_name' => 'Fungsi Pengereman (Rem Parkir)', 'max_score' => 10, 'is_critical' => true],
            ['item_name' => 'Kondisi Ban (Tekanan & Alur)', 'max_score' => 15, 'is_critical' => true],
            ['item_name' => 'Lampu Utama & Lampu Sein', 'max_score' => 10, 'is_critical' => true],
            ['item_name' => 'Wiper Kaca Depan', 'max_score' => 5, 'is_critical' => false],
            ['item_name' => 'Level Oli Mesin & Air Radiator', 'max_score' => 10, 'is_critical' => true],
            ['item_name' => 'Fungsi Klakson', 'max_score' => 2, 'is_critical' => false],
        ];

        foreach ($itemsMesin as $item) {
            InspectionItem::create(array_merge($item, ['category_id' => $catMesin->id]));
        }

        // 2. Kategori: Kabin & Fasilitas
        $catKabin = InspectionCategory::create([
            'name' => 'Kabin & Fasilitas',
            'target_role_id' => $roleStewardess->id,
            'min_passing_percentage' => 80,
        ]);

        $itemsKabin = [
            ['item_name' => 'Fungsi AC (Suhu Dingin)', 'max_score' => 15, 'is_critical' => true],
            ['item_name' => 'Kebersihan Toilet', 'max_score' => 10, 'is_critical' => false],
            ['item_name' => 'Fungsi Reclining Seat', 'max_score' => 5, 'is_critical' => false],
            ['item_name' => 'Audio & Video (Entertainment)', 'max_score' => 5, 'is_critical' => false],
            ['item_name' => 'Lampu Kabin & Lampu Baca', 'max_score' => 5, 'is_critical' => false],
            ['item_name' => 'Ketersediaan Selimut & Bantal', 'max_score' => 10, 'is_critical' => false],
        ];

        foreach ($itemsKabin as $item) {
            InspectionItem::create(array_merge($item, ['category_id' => $catKabin->id]));
        }

        // 3. Kategori: Dokumen Operasional
        $catDokumen = InspectionCategory::create([
            'name' => 'Dokumen Operasional',
            'target_role_id' => $roleMechanic->id,
            'min_passing_percentage' => 100,
        ]);

        $itemsDokumen = [
            ['item_name' => 'STNK Bus (Asli)', 'max_score' => 10, 'is_critical' => true],
            ['item_name' => 'Buku KIR (Masa Berlaku)', 'max_score' => 10, 'is_critical' => true],
            ['item_name' => 'Kartu Pengawasan (Trayek)', 'max_score' => 10, 'is_critical' => true],
            ['item_name' => 'SIM Driver A & B (Valid)', 'max_score' => 10, 'is_critical' => true],
        ];

        foreach ($itemsDokumen as $item) {
            InspectionItem::create(array_merge($item, ['category_id' => $catDokumen->id]));
        }

        $this->command->info('✅ Master Data Inspeksi P2H berhasil dibuat.');
    }
}
