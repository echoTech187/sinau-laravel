<?php

namespace App\Livewire\Pages\BusClasses;

use App\Models\BusClass;
use App\Models\Facility;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $confirmingBusClassDeletion = false;
    public ?int $busClassIdBeingDeleted = null;

    // Facility Management
    public bool $showingFacilityModal = false;
    public string $newFacilityName = '';
    public string $newFacilityIcon = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function busClasses()
    {
        return BusClass::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->with('facilities')
            ->withCount('buses')
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function facilities()
    {
        return Facility::all();
    }

    public function addFacility()
    {
        $this->validate([
            'newFacilityName' => 'required|string|max:50',
            'newFacilityIcon' => 'nullable|string|max:50',
        ]);

        Facility::create([
            'name' => $this->newFacilityName,
            'icon' => $this->newFacilityIcon ?: 'heroicon-o-sparkles',
        ]);

        $this->newFacilityName = '';
        $this->newFacilityIcon = '';
        $this->showingFacilityModal = false;
        $this->dispatch('notify', 'Fasilitas baru berhasil ditambahkan.', 'success');
    }

    public function deleteFacility($id)
    {
        Facility::find($id)->delete();
        $this->dispatch('notify', 'Fasilitas berhasil dihapus.', 'info');
    }

    public function confirmDeleteBusClass($id)
    {
        $this->confirmingBusClassDeletion = true;
        $this->busClassIdBeingDeleted = $id;
    }

    public function deleteBusClass()
    {
        if ($this->busClassIdBeingDeleted) {
            BusClass::find($this->busClassIdBeingDeleted)->delete();
            $this->confirmingBusClassDeletion = false;
            $this->busClassIdBeingDeleted = null;
            $this->dispatch('notify', 'Kelas Bus berhasil dihapus.', 'success');
        }
    }

    public function render()
    {
        return view('livewire.pages.bus-classes.index');
    }
}
