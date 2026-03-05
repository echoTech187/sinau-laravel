<?php

namespace App\Livewire\Pages\Maintenance\Rules;

use App\Models\Agent;
use App\Models\MaintenanceRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Layout('layouts::app')]
#[Title('Pengaturan Servis - Maintenance')]
class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public bool $showModal = false;
    
    // Form fields
    public ?int $ruleId = null;
    public ?string $taskName = '';
    public ?string $chassisBrand = null;
    public ?int $intervalKm = 10000;
    public ?int $toleranceKm = 1000;
    public ?int $estimatedHours = 2;
    public ?int $preferredAgentId = null;

    protected $rules = [
        'taskName' => 'required|string|max:255',
        'chassisBrand' => 'nullable|string|max:255',
        'intervalKm' => 'required|integer|min:1',
        'toleranceKm' => 'required|integer|min:0',
        'estimatedHours' => 'required|integer|min:1',
        'preferredAgentId' => 'nullable|exists:agents,id',
    ];

    public function create()
    {
        $this->reset(['ruleId', 'taskName', 'chassisBrand', 'intervalKm', 'toleranceKm', 'estimatedHours', 'preferredAgentId']);
        $this->showModal = true;
    }

    public function edit(MaintenanceRule $rule)
    {
        $this->ruleId = $rule->id;
        $this->taskName = $rule->task_name;
        $this->chassisBrand = $rule->chassis_brand;
        $this->intervalKm = $rule->interval_km;
        $this->toleranceKm = $rule->tolerance_km;
        $this->estimatedHours = $rule->estimated_hours;
        $this->preferredAgentId = $rule->preferred_agent_id;
        $this->showModal = true;
    }

    public function save()
    {
        $data = $this->validate();

        MaintenanceRule::updateOrCreate(
            ['id' => $this->ruleId],
            [
                'task_name' => $this->taskName,
                'chassis_brand' => $this->chassisBrand,
                'interval_km' => $this->intervalKm,
                'tolerance_km' => $this->toleranceKm,
                'estimated_hours' => $this->estimatedHours,
                'preferred_agent_id' => $this->preferredAgentId,
            ]
        );

        $this->success('Aturan perawatan berhasil disimpan.');
        $this->showModal = false;
        $this->resetPage();
    }

    public function delete(int $id)
    {
        $rule = MaintenanceRule::find($id);
        if ($rule) {
            $rule->delete();
            $this->success('Aturan perawatan berhasil dihapus.');
        }
    }

    public function render()
    {
        $rules = MaintenanceRule::with('preferredAgent')
            ->where('task_name', 'like', '%' . $this->search . '%')
            ->paginate(10);

        $agents = Agent::where('status', 'active')->get();

        return view('livewire.pages.maintenance.rules.index', [
            'maintenanceRules' => $rules,
            'agents' => $agents
        ]);
    }
}
