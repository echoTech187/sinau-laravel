<?php

namespace App\Livewire\Pages\Buses;

use App\Enums\BusStatus;
use App\Models\Bus;
use App\Models\BusClass;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app')]
class Index extends Component
{
    use WithPagination;

    public bool $confirmingBusDeletion = false;

    public ?int $busIdBeingDeleted = null;

    public string $search = '';

    public string $statusFilter = '';

    public string $classFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function buses()
    {
        return Bus::with(['busClass', 'seatLayout'])
            ->when($this->search, fn ($q) => $q->where('fleet_code', 'like', "%{$this->search}%")
                ->orWhere('plate_number', 'like', "%{$this->search}%")
                ->orWhere('name', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->classFilter, fn ($q) => $q->where('bus_class_id', $this->classFilter))
            ->latest()
            ->paginate(10);
    }

    #[Computed(persist: true)]
    public function busClasses()
    {
        return BusClass::all();
    }

    #[Computed]
    public function stats()
    {
        /** @var object|null $stats */
        $stats = Bus::toBase()
            ->selectRaw('COUNT(id) as total')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as active', [BusStatus::ACTIVE->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as maintenance', [BusStatus::MAINTENANCE->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as inactive', [BusStatus::INACTIVE->value])
            ->first();

        return [
            'total' => $stats?->total ?? 0,
            'active'      => $stats?->active ?? 0,
            'maintenance' => $stats?->maintenance ?? 0,
            'inactive'    => $stats?->inactive ?? 0,
        ];
    }

    #[Title('Daftar Armada - Master Data')]
    public function render()
    {
        return view('livewire.pages.buses.index');
    }

    public function confirmDeleteBus($id)
    {
        $this->busIdBeingDeleted = $id;
        $this->confirmingBusDeletion = true;
    }

    public function deleteBus()
    {
        if ($this->busIdBeingDeleted) {
            Bus::findOrFail($this->busIdBeingDeleted)->delete();
            $this->confirmingBusDeletion = false;
            $this->busIdBeingDeleted = null;
        }
    }
}

