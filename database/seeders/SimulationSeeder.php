<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Roles;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimulationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Branches
        $branchJkt = Branch::firstOrCreate(['name' => 'Jakarta'], ['location' => 'DKI Jakarta']);
        $branchSby = Branch::firstOrCreate(['name' => 'Surabaya'], ['location' => 'Jawa Timur']);
        $branchBdg = Branch::firstOrCreate(['name' => 'Bandung'], ['location' => 'Jawa Barat']);

        // 2. Create Dummy Transactions
        $branches = [$branchJkt, $branchSby, $branchBdg];

        // Bersihkan transaksi lama agar tidak dobel saat di-seed ulang
        Transaction::truncate();

        foreach ($branches as $branch) {
            for ($i = 1; $i <= 10; $i++) {
                Transaction::create([
                    'branch_id' => $branch->id,
                    'invoice_number' => 'INV-'.strtoupper(substr($branch->name, 0, 3)).'-'.rand(1000, 9999).'-'.$i,
                    'amount' => rand(100000, 5000000),
                    'description' => 'Penjualan ke Pelanggan '.rand(1, 100).' di '.$branch->name,
                ]);
            }
        }

        // 3. Assign Secondary Role to "Eko Susanto" with scope restricting to Jakarta
        $userEko = User::where('email', '=', 'ekosuesanto25@gmail.com', 'and')->first();
        if ($userEko) {
            $regionalRole = Roles::firstOrCreate(
                ['slug' => 'regional-manager'],
                ['role' => 'Regional Manager']
            );

            DB::table('user_has_roles')->updateOrInsert(
                ['user_id' => $userEko->id, 'role_id' => $regionalRole->id],
                [
                    'data_scope' => json_encode(['branch_id' => [$branchJkt->id]]),
                    'starts_at' => null,
                    'expires_at' => null,
                ]
            );

            $userEko->clearPermissionCache();
        }
    }
}
