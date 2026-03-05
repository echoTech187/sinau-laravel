<?php

namespace App\Livewire\Pages\Maintenance;

use App\Models\Bus;
use App\Models\MaintenanceLog;
use App\Services\MaintenanceService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Maintenance Dashboard')]
class Dashboard extends Component
{
    public array $busHealth = [];
    public array $expiringDocs = [];

    public function mount(MaintenanceService $service)
    {
        $buses = Bus::all();
        /** @var \App\Models\Bus $bus */
        foreach ($buses as $bus) {
            $health = $service->calculateBusHealth($bus);
            
            // Check if any rule is in warning or overdue
            $worstStatus = 'healthy';
            foreach ($health as $h) {
                if ($h['status'] === 'overdue') {
                    $worstStatus = 'overdue';
                    break;
                }
                if ($h['status'] === 'warning') {
                    $worstStatus = 'warning';
                }
            }

            if ($worstStatus !== 'healthy') {
                $this->busHealth[] = [
                    'bus' => $bus,
                    'worst_status' => $worstStatus,
                    'rules' => $health
                ];
            }
        }

        $this->expiringDocs = $service->getExpiringDocuments();
    }

    public function render()
    {
        return view('livewire.pages.maintenance.dashboard');
    }
}
