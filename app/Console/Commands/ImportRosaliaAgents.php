<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Location;
use App\Models\Agent;
use App\Models\AgentOperationalHour;
use App\Enums\AgentType;
use App\Enums\CommissionType;
use App\Enums\AgentStatus;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ImportRosaliaAgents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:rosalia-agents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import agents extracted from Rosalia Indah website into the database';

    /**
     * Parse the operational hours text into structured data for 7 days
     */
    private function parseOperationalHours($hoursText)
    {
        $hoursText = trim($hoursText ?? '');
        $lower = strtolower($hoursText);
        $result = [];

        // Catch closed/not operating
        if ($hoursText === '-' || str_contains($lower, 'tutup') || str_contains($lower, 'tidak beroperasi') || str_contains($lower, 'dialihkan') || str_contains($lower, 'titik turun') || str_contains($lower, 'gudang') || str_contains($lower, 'travel') || str_contains($lower, 'rest area') || str_contains($lower, 'penumpang saja')) {
            return ['status' => 'closed', 'data' => []];
        }

        // Catch 24 hours
        if (str_contains($lower, '24 jam')) {
            for ($i = 0; $i <= 6; $i++) {
                $result[] = [
                    'day' => $i,
                    'open_time' => '00:00:00',
                    'close_time' => '23:59:59', // or exactly 24:00:00 which sometimes DBs reject
                    'is_24_hours' => true,
                    'notes' => null
                ];
            }
            return ['status' => '24h', 'data' => $result];
        }

        // Catch standard ranges like "08:00 - 17:00" or "08.00 - 17.00"
        preg_match('/(\d{1,2})[\.:](\d{2})\s*-\s*(\d{1,2})[\.:](\d{2})/', $lower, $matches);
        if (count($matches) === 5) {
            $openHour = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $openMin = $matches[2];
            $closeHour = str_pad($matches[3], 2, '0', STR_PAD_LEFT);
            $closeMin = $matches[4];

            $openTime = "$openHour:$openMin:00";
            $closeTime = "$closeHour:$closeMin:00";

            for ($i = 0; $i <= 6; $i++) {
                $result[] = [
                    'day' => $i,
                    'open_time' => $openTime,
                    'close_time' => $closeTime,
                    'is_24_hours' => false,
                    'notes' => null
                ];
            }
            return ['status' => 'standard', 'data' => $result, 'original' => $hoursText];
        }

        // Fallback for complex/unknown formats
        for ($i = 0; $i <= 6; $i++) {
            $result[] = [
                'day' => $i,
                'open_time' => null,
                'close_time' => null,
                'is_24_hours' => false,
                'notes' => $hoursText
            ];
        }
        return ['status' => 'complex', 'data' => $result];
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Read JSON File
        $jsonPath = 'c:\\laragon\\www\\sinau-laravel-new\\agen.json';
        if (!file_exists($jsonPath)) {
            $this->error("File agen.json not found.");
            return;
        }

        $jsonData = json_decode(file_get_contents($jsonPath), true);
        if (!$jsonData) {
            $this->error("Failed to parse agen.json");
            return;
        }

        $this->info("Found " . count($jsonData) . " agents in JSON.");

        // We should first map existing agents to avoid duplicates.
        // We'll keep track of which agents were touched to delete others later.
        $touchedAgentIds = [];

        $addedCount = 0;
        $updatedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($jsonData as $item) {
                $agentName = trim($item['name']);
                $agentNameLower = strtolower($agentName);
                $phoneStr = implode(', ', $item['phone'] ?? []);

                // 1. Determine Agent Type
                $type = AgentType::PARTNER_GENERAL;
                if (
                    str_contains(strtolower($agentName), 'pool') ||
                    str_contains(strtolower($agentName), 'kantor pusat') ||
                    str_contains(strtolower($agentName), 'perwakilan')
                ) {
                    $type = AgentType::BRANCH_OFFICE;
                }

                // 2. Find Agent (Exact or LIKE)
                $agent = Agent::where('name', $agentName)->first();

                if (!$agent) {
                    // Try case-insensitive exact
                    $agent = Agent::whereRaw('LOWER(name) = ?', [$agentNameLower])->first();
                }

                if (!$agent) {
                    // Try LIKE search as requested
                    // Be careful with LIKE: we only match if it's unique enough or just take the first
                    $agent = Agent::where('name', 'LIKE', '%' . $agentName . '%')->first();
                }

                if ($agent) {
                    // Update existing
                    $agent->phone_number = $phoneStr ?: $agent->phone_number;
                    // Only update type if it's currently REGULAR to avoid overwriting manually set specific types
                    // if ($agent->type === AgentType::PARTNER_GENERAL) {
                    //     $agent->type = $type;
                    // }
                    $agent->save();
                    $updatedCount++;
                } else {
                    // Ensure Location exists for NEW agents
                    $provinceName = trim($item['province']);
                    $location = Location::firstOrCreate(
                        ['name' => $agentName . ' Area', 'province' => $provinceName],
                        ['city' => $agentName . ' City', 'address' => $item['address']]
                    );

                    // Create new agent!
                    $agent = Agent::create([
                        'agent_code' => 'AGT-' . strtoupper(Str::random(6)),
                        'location_id' => $location->id,
                        'name' => $agentName,
                        'phone_number' => $phoneStr,
                        'type' => $type,
                        'commission_type' => CommissionType::PERCENTAGE,
                        'commission_value' => 0,
                        'status' => AgentStatus::ACTIVE,
                    ]);
                    $addedCount++;
                }

                $touchedAgentIds[] = $agent->id;

                // Handle Operational Hours
                AgentOperationalHour::where('agent_id', $agent->id)->delete();
                $hoursData = $this->parseOperationalHours($item['operational_hours']);
                
                if ($hoursData['status'] === 'closed') {
                    $agent->status = AgentStatus::INACTIVE;
                    $agent->save();
                } else {
                    $agent->status = AgentStatus::ACTIVE;
                    $agent->save();
                    
                    foreach ($hoursData['data'] as $hourRow) {
                        $hourRow['agent_id'] = $agent->id;
                        AgentOperationalHour::create($hourRow);
                    }
                }
            }

            // 3. Delete agents NOT in the scraped data (Unnecessary agents)
            // As requested: "hapus data-data agen & lokasi yang tidak dibutuhkan"
            $deletedCount = Agent::whereNotIn('id', $touchedAgentIds)->count();
            Agent::whereNotIn('id', $touchedAgentIds)->delete();

            DB::commit();
            $this->info("Import completed!");
            $this->info("- Added: $addedCount");
            $this->info("- Updated: $updatedCount");
            $this->info("- Deleted: $deletedCount (Obsolete agents)");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Import failed: " . $e->getMessage() . " at line " . $e->getLine());
        }
    }
}
