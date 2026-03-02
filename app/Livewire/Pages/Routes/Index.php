<?php

namespace App\Livewire\Pages\Routes;

use App\Models\Route;
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

    public bool $confirmingRouteDeletion = false;
    public ?int $routeIdBeingDeleted = null;

    public string $search = '';
    public string $originFilter = '';
    public string $destFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingOriginFilter()
    {
        $this->resetPage();
    }

    public function updatingDestFilter()
    {
        $this->resetPage();
    }

    #[Computed]
    public function terminals()
    {
        return Location::whereHas('roles', function($q) {
            $q->whereIn('name', ['Terminal', 'Pool']);
        })->get();
    }

    #[Computed]
    public function stats()
    {
        return [
            'total' => Route::count('*'),
            'avg_distance' => Route::avg('distance_km') ?? 0,
        ];
    }

    #[Computed]
    public function routes()
    {
        return Route::with(['origin', 'destination'])->withCount('stops')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('route_code', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->originFilter, function ($query) {
                $query->where('origin_location_id', '=', $this->originFilter);
            })
            ->when($this->destFilter, function ($query) {
                $query->where('destination_location_id', '=', $this->destFilter);
            })
            ->latest()
            ->paginate(10);
    }

    public function confirmDeleteRoute($id)
    {
        $this->routeIdBeingDeleted = $id;
        $this->confirmingRouteDeletion = true;
    }

    public function deleteRoute()
    {
        $route = Route::findOrFail($this->routeIdBeingDeleted);
        // Cascade manually since we might not have cascading on DB level depending on migration
        $route->stops()->delete();
        $route->delete();

        $this->confirmingRouteDeletion = false;
        $this->routeIdBeingDeleted = null;
        
        session()->flash('message', 'Data Rute beserta Titik Pemberhentiannya berhasil dihapus!');
    }

    #[Title('Master Data Rute')]
    public function render()
    {
        return view('livewire.pages.routes.index');
    }
}

