<?php

namespace App\Livewire\Pages\Locations;

use App\Models\Location;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $typeFilter = '';

    public bool $confirmingLocationDeletion = false;

    public ?int $locationIdBeingDeleted = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function locations()
    {
        return Location::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('city', 'like', '%'.$this->search.'%')
                    ->orWhere('province', 'like', '%'.$this->search.'%');
            }, [])
            ->when($this->typeFilter, function ($query) {
                // Assuming we filter by location roles if needed,
                // but for now let's just use a simple name/city search
            }, [])
            ->with('roles')
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function stats()
    {
        return [
            'total' => Location::count('id'),
            'with_maintenance' => Location::where('has_maintenance_facility', '=', true, 'and')->count('id'),
            'cities' => Location::distinct('city')->count('city'),
        ];
    }

    public function confirmDeleteLocation($id)
    {
        $this->confirmingLocationDeletion = true;
        $this->locationIdBeingDeleted = $id;
    }

    public function deleteLocation()
    {
        if ($this->locationIdBeingDeleted) {
            Location::find($this->locationIdBeingDeleted, 'id')->delete();
            $this->confirmingLocationDeletion = false;
            $this->locationIdBeingDeleted = null;
            $this->dispatch('notify', 'Lokasi berhasil dihapus.', 'success');
        }
    }

    public function render()
    {
        return view('livewire.pages.locations.index');
    }
}

