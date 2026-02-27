<?php

namespace Database\Seeders;

use App\Models\Modules;
use App\Models\Permissions as Permission;
use App\Models\Roles;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    /**
     * Jalankan database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'icon' => 'heroicon-o-home', // Asumsi menggunakan Heroicons
                'order' => 1,
                'is_active' => true,
                'description' => 'Ringkasan performa sistem dan statistik utama.',
            ],
            [
                'name' => 'Master Data',
                'slug' => 'master-data',
                'icon' => 'heroicon-o-cube',
                'order' => 2,
                'is_active' => true,
                'description' => 'Pengelolaan data produk, kategori, dan stok barang.',
            ],
            [
                'name' => 'Keuangan',
                'slug' => 'finance',
                'icon' => 'heroicon-o-banknotes',
                'order' => 3,
                'is_active' => true,
                'description' => 'Manajemen transaksi, invoice, dan laporan keuangan.',
            ],
            [
                'name' => 'Kepegawaian',
                'slug' => 'hrd',
                'icon' => 'heroicon-o-users',
                'order' => 4,
                'is_active' => true,
                'description' => 'Database karyawan, kehadiran, dan payroll.',
            ],
            [
                'name' => 'Sistem & RBAC',
                'slug' => 'system',
                'icon' => 'heroicon-o-cog',
                'order' => 5,
                'is_active' => true,
                'description' => 'Konfigurasi hak akses, manajemen user, dan log audit.',
            ],
        ];

        foreach ($modules as $module) {
            Modules::updateOrCreate(
                ['slug' => $module['slug']], // Cek berdasarkan slug agar tidak duplikat
                $module
            );
        }

        // 2. Create Permissions
        $p1 = Permission::updateOrCreate(
            ['slug' => 'product.index'],
            ['name' => 'Lihat Produk', 'module_id' => 1]
        );
        $p2 = Permission::updateOrCreate(
            ['slug' => 'product.create'],
            ['name' => 'Tambah Produk', 'module_id' => 1]
        );
        $p3 = Permission::updateOrCreate(
            ['slug' => 'finance.approve'],
            ['name' => 'Setujui Bayar', 'module_id' => 3]
        );

        // 3. Assign Permissions to Roles
        Roles::find(1)?->permissions()->attach(array_filter([$p1?->id, $p2?->id, $p3?->id]));
        Roles::find(2)?->permissions()->attach(array_filter([$p1?->id, $p2?->id])); // Hanya lihat
        Roles::find(3)?->permissions()->attach(array_filter([$p3?->id]));

        // 4. Assign Modules to Roles
        // Roles::find(1)->modules()->attach([1, 2, 3, 4, 5]);
        // Roles::find(2)->modules()->attach([1, 2, 3, 4]);
        // Roles::find(3)->modules()->attach([1, 3, 4]);
    }
}
