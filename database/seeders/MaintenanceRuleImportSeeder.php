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

        // 1. Process Mercy & Scania (Sheet 0)
        $this->processMercyScania($spreadsheet->getSheet(0));

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
        $mercyKmCols = $data[4] ?? []; // Row 4 has KM for Mercy
        $this->importBlock($data, 5, 30, $mercyKmCols, 'Mercedes-Benz');

        // Scania block
        $scaniaKmCols = $data[43] ?? []; // Row 43 has KM for Scania
        $this->importBlock($data, 45, 81, $scaniaKmCols, 'Scania');
    }

    private function importBlock($data, $startRow, $endRow, $kmCols, $brand)
    {
        // Clean KM cols keys (remove empty and map to integer values)
        $intervals = [];
        foreach ($kmCols as $col => $val) {
            if ($val && preg_match('/(\d+[,.]?\d*)/', $val, $m)) {
                $km = (int) str_replace([',', '.'], '', $m[1]);
                $intervals[$col] = $km;
            }
        }

        for ($i = $startRow; $i <= $endRow; $i++) {
            $row = $data[$i] ?? null;
            if (!$row || empty($row['B'])) continue;

            $taskName = trim($row['B']);
            $minKm = null;

            // Find smallest KM interval marked with x, o, or v
            foreach ($intervals as $col => $km) {
                $mark = strtolower($row[$col] ?? '');
                if (in_array($mark, ['x', 'o', 'v', 'stel'])) {
                    if ($minKm === null || $km < $minKm) {
                        $minKm = $km;
                    }
                }
            }

            if ($minKm) {
                MaintenanceRule::updateOrCreate(
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

        foreach ($taskNames as $col => $name) {
            $minKm = null;
            // Iterate down rows 6-27 to find the first mark
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
                }
            }

            if ($minKm) {
                MaintenanceRule::updateOrCreate(
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
            }
        }
    }
}
