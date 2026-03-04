<?php

namespace App\Livewire\Pages\BusClasses;

use App\Models\BusClass;
use App\Models\Facility;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

use Livewire\Attributes\Title;

#[Title('Daftar Kelas Bus')]
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

    public ?int $editingFacilityId = null;

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
            ->where('name', 'like', '%'.$this->search.'%', 'and')
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

    public function saveFacility()
    {
        $this->validate([
            'newFacilityName' => 'required|string|max:50',
            'newFacilityIcon' => 'nullable|string|max:50',
        ]);

        if ($this->editingFacilityId) {
            $facility = Facility::findOrFail($this->editingFacilityId);
            $facility->update([
                'name' => $this->newFacilityName,
                'icon' => $this->newFacilityIcon ?: 'heroicon-o-sparkles',
            ]);
            $message = 'Fasilitas berhasil diperbarui.';
        } else {
            Facility::create([
                'name' => $this->newFacilityName,
                'icon' => $this->newFacilityIcon ?: 'heroicon-o-sparkles',
            ]);
            $message = 'Fasilitas baru berhasil ditambahkan.';
        }

        $this->resetModal();

        unset($this->facilities);
        $this->dispatch('notify', [
            'title' => 'Berhasil',
            'message' => $message,
            'type' => 'success'
        ]);
    }

    public function openCreateModal()
    {
        $this->editingFacilityId = null;
        $this->newFacilityName = '';
        $this->newFacilityIcon = '';
        $this->showingFacilityModal = true;
    }

    public function editFacility($id)
    {
        $facility = Facility::findOrFail($id);
        $this->editingFacilityId = $id;
        $this->newFacilityName = $facility->name;
        $this->newFacilityIcon = $facility->icon;
        $this->showingFacilityModal = true;
    }

    public function resetModal()
    {
        $this->newFacilityName = '';
        $this->newFacilityIcon = '';
        $this->editingFacilityId = null;
        $this->showingFacilityModal = false;
    }

    public function deleteFacility($id)
    {
        Facility::find($id, 'id')->delete();
        unset($this->facilities);
        $this->dispatch('notify', [
            'title' => 'Terhapus',
            'message' => 'Fasilitas berhasil dihapus.',
            'type' => 'info'
        ]);
    }

    public function confirmDeleteBusClass($id)
    {
        $this->confirmingBusClassDeletion = true;
        $this->busClassIdBeingDeleted = $id;
    }

    public function deleteBusClass()
    {
        if ($this->busClassIdBeingDeleted) {
            BusClass::find($this->busClassIdBeingDeleted, 'id')->delete();
            $this->confirmingBusClassDeletion = false;
            $this->busClassIdBeingDeleted = null;
            unset($this->busClasses);
            $this->dispatch('notify', [
                'title' => 'Berhasil',
                'message' => 'Kelas Bus berhasil dihapus.',
                'type' => 'success'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.pages.bus-classes.index');
    }
}

