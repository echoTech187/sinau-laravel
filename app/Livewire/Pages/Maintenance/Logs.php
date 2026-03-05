<?php

namespace App\Livewire\Pages\Maintenance;

use App\Models\Bus;
use App\Models\MaintenanceLog;
use App\Models\MaintenanceRule;
use App\Models\Agent;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title('Maintenance Logs')]
class Logs extends Component
{
    use WithPagination;

    #[Url]
    public $bus_id = '';

    public bool $showCreateModal = false;
    
    // Form fields
    public $selectedBusId = '';
    public $ruleId = '';
    public $locationId = '';
    public $type = 'corrective';
    public $status = 'pending';
    public $issue = '';
    public $cost = 0;
    public $vendor = '';
    public $odoAtService = '';

    protected $rules = [
        'selectedBusId' => 'required|exists:buses,id',
        'ruleId' => 'nullable|exists:maintenance_rules,id',
        'locationId' => 'required|exists:agents,id',
        'type' => 'required',
        'status' => 'required',
        'issue' => 'required|string|min:5',
        'cost' => 'nullable|numeric|min:0',
        'vendor' => 'nullable|string',
        'odoAtService' => 'required|integer|min:0',
    ];

    public function mount()
    {
        if ($this->bus_id) {
            $this->selectedBusId = $this->bus_id;
            $this->showCreateModal = true;
            
            $bus = Bus::find((int) $this->bus_id);
            if ($bus) {
                $this->odoAtService = $bus->current_odometer;
            }
        }
    }

    public function saveLog()
    {
        $this->validate();

        MaintenanceLog::create([
            'bus_id' => $this->selectedBusId,
            'maintenance_rule_id' => $this->ruleId ?: null,
            'location_id' => $this->locationId,
            'reported_by_id' => Auth::id(),
            'type' => $this->type,
            'status' => $this->status,
            'issue_description' => $this->issue,
            'total_cost' => $this->cost,
            'vendor_name' => $this->vendor,
            'odometer_at_service' => $this->odoAtService,
            'resolved_at' => $this->status === 'resolved' ? now() : null,
        ]);

        $this->reset(['showCreateModal', 'selectedBusId', 'ruleId', 'locationId', 'issue', 'cost', 'vendor', 'odoAtService']);
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Berhasil', 'message' => 'Catatan perawatan berhasil disimpan.']);
    }

    public function render()
    {
        $query = MaintenanceLog::with(['bus', 'rule'])->orderBy('created_at', 'desc');

        if ($this->bus_id) {
            $query->where('bus_id', '=', $this->bus_id);
        }

        return view('livewire.pages.maintenance.logs', [
            'logs' => $query->paginate(10),
            'buses' => Bus::all(),
            'rulesList' => MaintenanceRule::all(),
            'locationsList' => Agent::all(),
        ]);
    }
}
