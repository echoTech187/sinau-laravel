<?php

namespace Database\Seeders;

use App\Models\MaintenanceRule;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MaintenanceRuleImportSeeder extends Seeder
{
    public function run(): void
    {
        $inputFileName = 'public/references/KM Perawatan.xlsx';

        if (!file_exists($inputFileName)) {
            $this->command->error("File not found: $inputFileName");
            return;
        }

        $spreadsheet = IOFactory::load($inputFileName);

        $mercyScaniaSheet = $spreadsheet->getSheetByName('STD KM Perawatan Mercy & Scania') 
            ?? $spreadsheet->getSheet(0);
        $this->processMercyScania($mercyScaniaSheet);

        // 2. Process Hino (Sheet "Hino")
        $hinoSheet = $spreadsheet->getSheetByName('Hino');
        if ($hinoSheet) {
            $this->processHino($hinoSheet);
        }

        $this->command->info("✓ Maintenance rules imported successfully from Excel.");
    }

    private function processMercyScania($sheet)
    {
        $data = $sheet->toArray(null, true, true, true);
        
        // Mercy block
        $mercyKmCols = $data[5] ?? []; // Row 5 (index 5) has KM numbers
        $this->importBlock($data, 6, 31, $mercyKmCols, 'Mercedes-Benz');

        // Scania block
        $scaniaKmCols = $data[44] ?? []; 
        $this->importBlock($data, 46, 82, $scaniaKmCols, 'Scania');
    }

    private function importBlock($data, $startRow, $endRow, $kmCols, $brand)
    {
        // Clean KM cols keys (remove empty and map to integer values)
        $intervals = [];
        $packages = [];
        foreach ($kmCols as $col => $val) {
            $cleanVal = str_replace([',', '.'], '', (string)$val);
            if ($val && preg_match('/(\d+)/', $cleanVal, $m)) {
                $km = (int) $m[1];
                $intervals[$col] = $km;
                
                // Create or find Package
                $pkgName = "Paket " . number_format($km, 0, ',', '.') . " KM";
                $packages[$col] = \App\Models\ServicePackage::firstOrCreate(
                    [
                        'chassis_brand' => $brand,
                        'km_interval' => $km
                    ],
                    [
                        'name' => $pkgName,
                        'is_major' => false,
                    ]
                );
            }
        }

        for ($i = $startRow; $i <= $endRow; $i++) {
            $row = $data[$i] ?? null;
            if (!$row || empty($row['B'])) continue;

            $taskName = trim($row['B']);
            $minKm = null;
            $packageIdsToAttach = [];

            // Find smallest KM interval marked with x, o, or v and collect packages
            foreach ($intervals as $col => $km) {
                $mark = strtolower($row[$col] ?? '');
                if (in_array($mark, ['x', 'o', 'v', 'stel'])) {
                    if ($minKm === null || $km < $minKm) {
                        $minKm = $km;
                    }
                    if (isset($packages[$col])) {
                        $packageIdsToAttach[] = $packages[$col]->id;
                    }
                }
            }

            if ($minKm) {
                $rule = MaintenanceRule::updateOrCreate(
                    [
                        'task_name' => $taskName,
                        'chassis_brand' => $brand,
                    ],
                    [
                        'interval_km' => $minKm,
                        'tolerance_km' => (int) ($minKm * 0.1), // Default 10% tolerance
                        'estimated_hours' => 2, // Default
                    ]
                );

                // Attach to packages
                if (!empty($packageIdsToAttach)) {
                    $rule->servicePackages()->syncWithoutDetaching($packageIdsToAttach);
                }
            }
        }
    }

    private function processHino($sheet)
    {
        $data = $sheet->toArray(null, true, true, true);
        
        // Hino structure: Rows 3/4/5 are headers (Tasks), Rows 6-27 are KM (Intervals)
        // We'll collect tasks from row 3/4/5
        $taskNames = [];
        foreach (range('C', 'Y') as $col) {
            $name = trim(($data[3][$col] ?? '') . ' ' . ($data[4][$col] ?? '') . ' ' . ($data[5][$col] ?? ''));
            $name = preg_replace('/\s+/', ' ', $name);
            if (!empty($name)) {
                $taskNames[$col] = $name;
            }
        }

        // First pre-create packages for rows 6-27
        $packages = [];
        for ($i = 6; $i <= 27; $i++) {
            $row = $data[$i] ?? null;
            if (!$row) continue;
            $kmVal = str_replace([',', '.'], '', $row['B'] ?? '');
            if ($kmVal) {
                $km = (int) $kmVal;
                $pkgName = "Paket " . number_format($km, 0, ',', '.') . " KM";
                $packages[$i] = \App\Models\ServicePackage::firstOrCreate(
                    [
                        'chassis_brand' => 'Hino',
                        'km_interval' => $km
                    ],
                    [
                        'name' => $pkgName,
                        'is_major' => false,
                    ]
                );
            }
        }

        foreach ($taskNames as $col => $name) {
            $minKm = null;
            $packageIdsToAttach = [];
            // Iterate down rows 6-27 to find the marks
            for ($i = 6; $i <= 27; $i++) {
                $row = $data[$i] ?? null;
                if (!$row) continue;
                
                $kmVal = str_replace([',', '.'], '', $row['B'] ?? '');
                $km = (int) $kmVal;
                
                $mark = strtolower($row[$col] ?? '');
                if (in_array($mark, ['x', 'o', 'v', 'stel', 'all', 'cek'])) {
                    if ($minKm === null || $km < $minKm) {
                        $minKm = $km;
                    }
                    if (isset($packages[$i])) {
                        $packageIdsToAttach[] = $packages[$i]->id;
                    }
                }
            }

            if ($minKm) {
                $rule = MaintenanceRule::updateOrCreate(
                    [
                        'task_name' => $name,
                        'chassis_brand' => 'Hino',
                    ],
                    [
                        'interval_km' => $minKm,
                        'tolerance_km' => (int) ($minKm * 0.1),
                        'estimated_hours' => 2,
                    ]
                );

                if (!empty($packageIdsToAttach)) {
                    $rule->servicePackages()->syncWithoutDetaching($packageIdsToAttach);
                }
            }
        }
    }
}
