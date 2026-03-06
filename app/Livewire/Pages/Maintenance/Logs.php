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
    public $packageId = '';
    public $ruleIds = [];
    public $locationId = '';
    public $type = 'corrective';
    public $status = 'pending';
    public $issue = '';
    public $cost = 0;
    public $vendor = '';
    public $odoAtService = '';

    protected $rules = [
        'selectedBusId' => 'required|exists:buses,id',
        'packageId' => 'nullable|exists:service_packages,id',
        'ruleIds' => 'required|array|min:1',
        'ruleIds.*' => 'exists:maintenance_rules,id',
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

    public function updatedPackageId($value)
    {
        if ($value) {
            $package = \App\Models\ServicePackage::with('maintenanceRules')->find($value);
            if ($package) {
                $this->ruleIds = $package->maintenanceRules->pluck('id')->toArray();
                // Optionally auto-set the type to preventive if it's a scheduled package
                $this->type = 'preventive';
            }
        } else {
            $this->ruleIds = [];
        }
    }

    public function updatedSelectedBusId($value)
    {
        if ($value) {
            $bus = Bus::find((int) $value);
            if ($bus) {
                $this->odoAtService = $bus->current_odometer;
            }
        }
    }

    public function saveLog()
    {
        $this->validate();

        $costPerRule = 0;
        if ($this->cost > 0 && count($this->ruleIds) > 0) {
            $costPerRule = $this->cost / count($this->ruleIds); // Split cost evenly
        }

        foreach ($this->ruleIds as $ruleId) {
            MaintenanceLog::create([
                'bus_id' => $this->selectedBusId,
                'maintenance_rule_id' => $ruleId,
                'location_id' => $this->locationId,
                'reported_by_id' => Auth::id(),
                'type' => $this->type,
                'status' => $this->status,
                'issue_description' => $this->issue,
                'total_cost' => $costPerRule,
                'vendor_name' => $this->vendor,
                'odometer_at_service' => $this->odoAtService,
                'resolved_at' => $this->status === 'resolved' ? now() : null,
            ]);
        }

        $this->reset(['showCreateModal', 'selectedBusId', 'packageId', 'ruleIds', 'locationId', 'issue', 'cost', 'vendor', 'odoAtService']);
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Berhasil', 'message' => 'Catatan perawatan berhasil disimpan.']);
    }

    public function render()
    {
        $query = MaintenanceLog::with(['bus', 'rule'])->orderBy('created_at', 'desc');

        if ($this->bus_id) {
            $query->where('bus_id', '=', $this->bus_id);
        }

        $packagesList = \App\Models\ServicePackage::orderBy('name')->get();

        return view('livewire.pages.maintenance.logs', [
            'logs' => $query->paginate(10),
            'buses' => Bus::all(),
            'rulesList' => MaintenanceRule::orderBy('task_name')->get(),
            'locationsList' => Agent::all(),
            'packagesList' => $packagesList,
        ]);
    }
}
