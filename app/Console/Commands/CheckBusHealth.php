<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckBusHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-bus-health';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa kesehatan armada bus dan jatuh tempo dokumen.';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\MaintenanceService $service): void
    {
        $this->info('--- Memeriksa Kesehatan Armada ---');
        
        $buses = \App\Models\Bus::all();
        $dueCount = 0;

        /** @var \App\Models\Bus $bus */
        foreach ($buses as $bus) {
            $health = $service->calculateBusHealth($bus);
            $isDue = collect($health)->contains(fn($h) => $h['status'] !== 'healthy');

            if ($isDue) {
                $this->warn("Bus {$bus->fleet_code}: Butuh Perawatan!");
                $dueCount++;
            }
        }

        $expiringDocs = $service->getExpiringDocuments();
        if (!empty($expiringDocs)) {
            $this->error("Terdapat " . count($expiringDocs) . " bus dengan dokumen segera habis!");
        }

        $this->info("Pemeriksaan selesai. Total bus butuh perhatian: " . ($dueCount + count($expiringDocs)));
    }
}
