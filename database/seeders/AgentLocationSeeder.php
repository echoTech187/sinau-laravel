<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\AgentOperationalHour;
use App\Models\Location;
use Illuminate\Database\Seeder;

class AgentLocationSeeder extends Seeder
{
    // Track current sheet area for province fallback
    private string $currentArea = '';

    // Known Indonesian province keywords for detection
    private array $provinceMap = [
        'jawa timur'                => 'Jawa Timur',
        'jawa tengah'               => 'Jawa Tengah',
        'jawa barat'                => 'Jawa Barat',
        'daerah istimewa yogyakarta'=> 'D.I. Yogyakarta',
        'diy'                       => 'D.I. Yogyakarta',
        'yogyakarta'                => 'D.I. Yogyakarta',
        'dki jakarta'               => 'DKI Jakarta',
        'jakarta'                   => 'DKI Jakarta',
        'banten'                    => 'Banten',
        'sumatera selatan'          => 'Sumatera Selatan',
        'lampung'                   => 'Lampung',
        'sumatera utara'            => 'Sumatera Utara',
    ];

    // City terms to strip before extracting city name
    private array $cityPrefixes = ['kota', 'kab.', 'kabupaten', 'kec.', 'kecamatan', 'kel.', 'kelurahan'];

    public function run(): void
    {
        $dataFile = 'c:\temp\excel_data.txt';
        if (!file_exists($dataFile)) {
            $this->command->error("Data file not found: $dataFile");
            return;
        }

        // Truncate existing seeded data (respecting FK order)
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
        \Illuminate\Support\Facades\DB::table('agent_operational_hours')->delete();
        \Illuminate\Support\Facades\DB::table('agents')->delete();
        \Illuminate\Support\Facades\DB::table('locations')->where('name', 'like', 'Lokasi %')->delete();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $lines = file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $count = 0;

        foreach ($lines as $line) {
            // Detect sheet area headers
            if (str_contains($line, 'AREA TIMUR'))   { $this->currentArea = 'TIMUR';   continue; }
            if (str_contains($line, 'AREA TENGAH'))  { $this->currentArea = 'TENGAH';  continue; }
            if (str_contains($line, 'AREA BARAT'))   { $this->currentArea = 'BARAT';   continue; }
            if (str_contains($line, 'AREA SUMATRA')) { $this->currentArea = 'SUMATRA'; continue; }

            if (!str_starts_with($line, 'ROW ')) continue;

            $content = preg_replace('/^ROW \d+: /', '', $line);
            $parts   = explode(' | ', $content);

            if (count($parts) < 5) continue;

            $no       = trim($parts[0]);
            $name     = trim($parts[1]);
            $address  = trim($parts[2]);
            $phone    = trim($parts[3]);
            $coords   = trim($parts[4]);
            $hoursRaw = isset($parts[5]) ? trim($parts[5]) : '';

            // Skip headers / placeholders / summary rows
            if (
                $name === '' || $name === 't' || strtolower($name) === 'agen'
                || $no === 'JUMLAH REKAPAN' || $no === 'NO'
            ) {
                continue;
            }

            // Parse coordinates
            $lat = $lng = null;
            if (str_contains($coords, ',')) {
                [$latRaw, $lngRaw] = explode(',', $coords);
                if (is_numeric(trim($latRaw))) $lat = trim($latRaw);
                if (is_numeric(trim($lngRaw))) $lng = trim($lngRaw);
            }

            // Extract city & province from address
            $city     = $this->extractCity($address);
            $province = $this->extractProvince($address);

            // Create Location
            $location = Location::create([
                'name'                   => 'Lokasi ' . $name,
                'address'                => $address,
                'city'                   => $city,
                'province'               => $province,
                'latitude'               => $lat,
                'longitude'              => $lng,
                'geofence_type'          => 'circular',
                'geofence_radius_meter'  => 500,
                'qr_code_gate'           => null,
                'has_maintenance_facility' => false,
            ]);

            // Determine Agent type
            $type = (stripos($name, 'pool') !== false || stripos($name, 'pusat') !== false)
                ? 'branch_office'
                : 'partner_general';

            // Clean phone: remove label prefixes and take first 50 chars
            $phone = preg_replace('/^(HP\s*[:.]?|Telp\s*[:.]?|Phone\s*[:.]?|No\s+telp\s*[:.]?|WA\s*[:.]?)/i', '', $phone);
            $phone = preg_replace('/\s+/', ' ', trim($phone));
            $phone = mb_substr($phone, 0, 50);

            // Create Agent
            $agent = Agent::create([
                'agent_code'       => 'AGT-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT),
                'location_id'      => $location->id,
                'name'             => $name,
                'phone_number'     => $phone,
                'type'             => $type,
                'commission_type'  => 'percentage',
                'commission_value' => 0,
                'status'           => 'active',
            ]);

            // Parse & save operational hours
            $this->parseAndCreateOperationalHours($agent, $hoursRaw);

            $count++;
        }

        $this->command->info("✓ Seeded {$count} agents and locations with operational hours.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Address parsing helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function extractCity(string $address): string
    {
        if (empty($address) || $address === 't') return '';

        $parts = array_map('trim', explode(',', $address));
        $parts = array_filter($parts, fn ($p) => $p !== '');
        $parts = array_values($parts);

        // Walk from the end and find the first part that looks like a city name
        // (not a zip code, not a province indicator, not empty)
        foreach (array_reverse($parts) as $part) {
            // Remove zip codes embedded in the part
            $cleaned = preg_replace('/\b\d{5}\b/', '', $part);
            $cleaned = trim($cleaned, " \t,");

            if (empty($cleaned)) continue;

            // Remove city/kab. prefixes for cleanliness
            foreach ($this->cityPrefixes as $prefix) {
                $cleaned = preg_replace('/^' . preg_quote($prefix, '/') . '\s+/i', '', $cleaned);
            }

            $cleaned = trim($cleaned);
            if (strlen($cleaned) < 2) continue;

            // Skip if it looks like a full province name
            if (stripos($cleaned, 'jawa') === 0 || stripos($cleaned, 'sumatera') === 0
                || strtolower($cleaned) === 'yogyakarta'
                || strtolower($cleaned) === 'banten'
            ) {
                continue;
            }

            return ucwords(strtolower($cleaned));
        }

        return '';
    }

    private function extractProvince(string $address): string
    {
        if (empty($address)) return $this->areaDefaultProvince();

        $lower = strtolower($address);

        foreach ($this->provinceMap as $keyword => $province) {
            if (str_contains($lower, $keyword)) {
                return $province;
            }
        }

        return $this->areaDefaultProvince();
    }

    private function areaDefaultProvince(): string
    {
        return match ($this->currentArea) {
            'TIMUR'   => 'Jawa Timur',
            'TENGAH'  => 'Jawa Tengah',
            'BARAT'   => 'Jawa Barat',
            'SUMATRA' => 'Lampung',
            default   => '',
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Operational hours parsing
    // ─────────────────────────────────────────────────────────────────────────

    private function parseAndCreateOperationalHours(Agent $agent, string $hoursRaw): void
    {
        $raw = strtolower(trim($hoursRaw));

        if (empty($raw) || in_array($raw, ['-', 't'])
            || str_contains($raw, 'tidak aktif')
            || str_contains($raw, 'blm aktif')
            || str_contains($raw, 'sudah di hubungi')
        ) {
            return;
        }

        $is24 = str_contains($raw, '24 jam');

        // Default open/close times from first HH.MM - HH.MM match
        $baseOpen = $baseClose = null;
        if (preg_match('/(\d{1,2})[.:](\d{2})\s*(?:s\/d|-)\s*(\d{1,2})[.:](\d{2})/', $raw, $m)) {
            $baseOpen  = sprintf('%02d:%02d:00', $m[1], $m[2]);
            $baseClose = sprintf('%02d:%02d:00', $m[3], $m[4]);
        }

        // Parse per-day overrides (format: "Senin-Sabtu: 08.00 - 17.00 / Minggu: 08.00 - 16.00")
        $dayOverrides  = $this->parseDayOverrides($raw, $baseOpen, $baseClose);

        // Day index: 0=Senin … 6=Minggu (matches Monday=0 style used in the table)
        for ($day = 0; $day <= 6; $day++) {
            [$open, $close, $closed, $notes] = $dayOverrides[$day];

            AgentOperationalHour::create([
                'agent_id'    => $agent->id,
                'day'         => $day,
                'open_time'   => $is24 || $closed ? null : $open,
                'close_time'  => $is24 || $closed ? null : $close,
                'is_24_hours' => $is24,
                'notes'       => $notes,
            ]);
        }
    }

    /**
     * Returns an array of 7 entries [open, close, isClosed, notes] indexed 0–6 (Mon–Sun).
     */
    private function parseDayOverrides(string $raw, ?string $baseOpen, ?string $baseClose): array
    {
        // Indonesian day names  → day index (0=Mon, 6=Sun)
        $dayIndex = [
            'senin' => 0, 'selasa' => 1, 'rabu' => 2, 'kamis' => 3,
            'jumat' => 4, 'sabtu' => 5, 'minggu' => 6,
        ];

        // Default: all days use baseOpen/baseClose
        $result = [];
        for ($d = 0; $d <= 6; $d++) {
            $result[$d] = [$baseOpen, $baseClose, false, null];
        }

        // Weekend / Minggu tutup?
        if (str_contains($raw, 'minggu tutup')) {
            $result[6] = [null, null, true, 'Tutup'];
        }

        // Scan for segment like "Senin - Sabtu : 08.00 - 17.00"
        // Possible separators between segments: " / " or newline
        $segments = preg_split('/\s*[\/\n]\s*/', $raw);
        foreach ($segments as $seg) {
            $seg = trim($seg);

            // Match: DayFrom[-DayTo]: HH.MM - HH.MM
            if (!preg_match('/(' . implode('|', array_keys($dayIndex)) . ')(?:\s*[-–]\s*(' . implode('|', array_keys($dayIndex)) . '))?\s*[:：]\s*(\d{1,2})[.:](\d{2})\s*[-–]\s*(\d{1,2})[.:](\d{2})/u', $seg, $m)) {
                continue;
            }

            $from  = $m[1];
            $to    = $m[2] ?: $from;
            $open  = sprintf('%02d:%02d:00', $m[3], $m[4]);
            $close = sprintf('%02d:%02d:00', $m[5], $m[6]);

            $fromIdx = $dayIndex[$from];
            $toIdx   = $dayIndex[$to];

            // Handle wrap-around (shouldn't happen much, but be safe)
            if ($fromIdx <= $toIdx) {
                for ($d = $fromIdx; $d <= $toIdx; $d++) {
                    $result[$d] = [$open, $close, false, null];
                }
            } else {
                // e.g. Sabtu - Minggu spanning end of week
                for ($d = $fromIdx; $d <= 6; $d++) {
                    $result[$d] = [$open, $close, false, null];
                }
            }
        }

        return $result;
    }
}
