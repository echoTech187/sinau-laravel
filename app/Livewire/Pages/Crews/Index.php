<?php

namespace App\Livewire\Pages\Crews;

use App\Models\Crew;
use App\Models\CrewPosition;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app')]
class Index extends Component
{
    use WithPagination;

    public bool $confirmingCrewDeletion = false;
    public ?int $crewIdBeingDeleted = null;

    public string $search = '';
    public string $positionFilter = '';
    public string $statusFilter = '';

    public function mount()
    {
        // 
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPositionFilter()
    {
        $this->resetPage();
    }

    #[Computed]
    public function crewPositions()
    {
        return CrewPosition::all();
    }

    #[Computed]
    public function stats()
    {
        return [
            'total' => Crew::count('id'),
            'active' => Crew::where('status', '=', 'active', 'and')->count('id'),
            'on_leave' => Crew::where('status', '=', 'on_leave', 'and')->count('id'),
            'inactive' => Crew::where('status', '=', 'inactive', 'and')->orWhere('status', '=', 'suspended', 'or')->count('id'),
        ];
    }

    #[Computed]
    public function crews()
    {
        return Crew::with('position')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('employee_number', 'like', '%' . $this->search . '%')
                      ->orWhere('phone_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->positionFilter, function ($query) {
                $query->where('crew_position_id', $this->positionFilter);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(10);
    }

    public function confirmDeleteCrew($id)
    {
        $this->crewIdBeingDeleted = $id;
        $this->confirmingCrewDeletion = true;
    }

    public function deleteCrew()
    {
        $crew = Crew::findOrFail($this->crewIdBeingDeleted);
        $crew->delete();

        $this->confirmingCrewDeletion = false;
        $this->crewIdBeingDeleted = null;
        unset($this->crews);
        
        $this->dispatch('notify', ['title' => 'Berhasil', 'message' => 'Data Kru berhasil dihapus!', 'type' => 'success']);
    }

    #[Title('Master Data Kru')]
    public function render()
    {
        return view('livewire.pages.crews.index');
    }
}

