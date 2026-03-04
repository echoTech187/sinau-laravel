<?php

namespace App\Livewire\Pages\Agents;

use App\Models\Agent;
use App\Models\Location;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app')]
class Index extends Component
{
    use WithPagination;

    public bool $confirmingAgentDeletion = false;

    public ?int $agentIdBeingDeleted = null;

    public string $search = '';

    public string $typeFilter = '';

    public string $statusFilter = '';

    public string $locationFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingLocationFilter()
    {
        $this->resetPage();
    }

    #[Computed]
    public function locations()
    {
        return Location::whereHas('roles', function ($q) {
            $q->where('name', '=', 'Agen');
        })->get();
    }

    #[Computed]
    public function stats()
    {
        return [
            'total' => Agent::count('*'),
            'active' => Agent::where('status', '=', 'active', 'and')->count('*'),
            'branch' => Agent::where('type', '=', 'branch_office', 'and')->count('*'),
            'partner' => Agent::whereIn('type', ['partner_exclusive', 'partner_general'], 'and', false)->count('*'),
        ];
    }

    #[Computed]
    public function agents()
    {
        return Agent::with(['location', 'parentBranch', 'balance', 'operationalHours'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('agent_code', 'like', '%'.$this->search.'%')
                        ->orWhere('phone_number', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', '=', $this->typeFilter);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', '=', $this->statusFilter);
            })
            ->when($this->locationFilter, function ($query) {
                $query->where('location_id', '=', $this->locationFilter);
            })
            ->latest()
            ->paginate(10);
    }

    public function confirmDeleteAgent($id)
    {
        $this->agentIdBeingDeleted = $id;
        $this->confirmingAgentDeletion = true;
    }

    public function deleteAgent()
    {
        $agent = Agent::findOrFail($this->agentIdBeingDeleted);
        $agent->delete();

        $this->confirmingAgentDeletion = false;
        $this->agentIdBeingDeleted = null;

        $this->dispatch('notify', ['title' => 'Berhasil', 'message' => 'Data Agen berhasil dihapus!', 'type' => 'success']);
    }
    
    public function sync()
    {
        // Simulate sync delay
        usleep(800000); 
        
        $this->resetPage();
        unset($this->agents);
        unset($this->stats);
        
        $this->dispatch('notify', [
            'title' => 'Sinkronisasi Berhasil',
            'message' => 'Data agen telah diperbarui dari server.',
            'type' => 'success'
        ]);
    }

    #[Title('Master Data Agen & Cabang')]
    public function render()
    {
        return view('livewire.pages.agents.index');
    }
}

