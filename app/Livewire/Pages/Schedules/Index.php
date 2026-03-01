<?php

namespace App\Livewire\Pages\Schedules;

use App\Models\Bus;
use App\Models\Schedule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app.sidebar')]
#[Title('Daftar Jadwal Keberangkatan')]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $filter_bus = '';

    public $filter_status = '';

    public $date_from = '';

    public $date_to = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filter_bus' => ['except' => ''],
        'filter_status' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function schedules()
    {
        return Schedule::query()
            ->with(['route', 'bus', 'crews.crew'])
            ->when($this->search, function ($query) {
                $query->whereHas('route', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('route_code', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filter_bus, fn ($q) => $q->where('bus_id', $this->filter_bus))
            ->when($this->filter_status, fn ($q) => $q->where('status', $this->filter_status))
            ->when($this->date_from, fn ($q) => $q->whereDate('departure_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('departure_date', '<=', $this->date_to))
            ->orderBy('departure_date', 'desc')
            ->orderBy('departure_time', 'desc')
            ->paginate(10);
    }

    #[Computed]
    public function buses()
    {
        return Bus::orderBy('name', 'asc')->get();
    }

    #[Computed]
    public function stats()
    {
        return [
            'total' => Schedule::count('id'),
            'scheduled' => Schedule::where('status', '=', 'cancelled', 'and')->count(),
            'on_the_way' => Schedule::where('status', '=', 'on_the_way', 'and')->count(),
            'arrived' => Schedule::where('status', '=', 'arrived', 'and')->count(),
        ];
    }

    public function deleteSchedule($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        session()->flash('message', 'Jadwal berhasil dihapus secara logis.');
    }

    public function render()
    {
        return view('livewire.pages.schedules.index');
    }
}
