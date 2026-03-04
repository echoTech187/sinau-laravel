<?php

namespace App\Livewire\Pages\Locations;

use App\Models\Location;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Daftar Lokasi')]
class Index extends Component
{
    use WithPagination;

    public string $search          = '';
    public string $provinceFilter  = '';
    public string $maintenance     = '';

    public bool $confirmingLocationDeletion = false;
    public ?int $locationIdBeingDeleted     = null;

    protected $queryString = [
        'search'         => ['except' => ''],
        'provinceFilter' => ['except' => ''],
        'maintenance'    => ['except' => ''],
    ];

    public function updatingSearch(): void        { $this->resetPage(); }
    public function updatingProvinceFilter(): void { $this->resetPage(); }
    public function updatingMaintenance(): void    { $this->resetPage(); }

    #[Computed]
    public function provinces(): array
    {
        return Location::query()
            ->whereNotNull('province')
            ->where('province', '!=', '')
            ->distinct()
            ->orderBy('province')
            ->pluck('province')
            ->toArray();
    }

    #[Computed]
    public function locations()
    {
        return Location::query()
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('city', 'like', '%'.$this->search.'%')
                  ->orWhere('province', 'like', '%'.$this->search.'%')
            )
            ->when($this->provinceFilter, fn($q) =>
                $q->where('province', $this->provinceFilter)
            )
            ->when($this->maintenance !== '', fn($q) =>
                $q->where('has_maintenance_facility', (bool) $this->maintenance)
            )
            ->with('roles')
            ->latest()
            ->paginate(12);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total'            => Location::count(),
            'with_maintenance' => Location::where('has_maintenance_facility', true)->count(),
            'cities'           => Location::distinct('city')->count('city'),
        ];
    }

    public function confirmDeleteLocation(int $id): void
    {
        $this->confirmingLocationDeletion = true;
        $this->locationIdBeingDeleted     = $id;
    }

    public function deleteLocation(): void
    {
        if ($this->locationIdBeingDeleted) {
            Location::findOrFail($this->locationIdBeingDeleted)->delete();
            $this->confirmingLocationDeletion = false;
            $this->locationIdBeingDeleted     = null;
            unset($this->locations);
            $this->dispatch('notify', ['title' => 'Berhasil', 'message' => 'Lokasi berhasil dihapus.', 'type' => 'success']);
        }
    }

    public function render()
    {
        return view('livewire.pages.locations.index');
    }
}

