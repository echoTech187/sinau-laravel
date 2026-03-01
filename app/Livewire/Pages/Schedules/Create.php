<?php

namespace App\Livewire\Pages\Schedules;

use App\Livewire\Forms\ScheduleForm;
use App\Models\Bus;
use App\Models\Route as BusRoute;
use App\Models\Crew;
use App\Models\CrewPosition;
use App\Models\Location;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app.sidebar')]
#[Title('Buat Jadwal Baru')]
class Create extends Component
{
    public ScheduleForm $form;

    public function mount()
    {
        $this->form->departure_date = now()->format('Y-m-d');
    }

    public function updatedFormRouteId($value)
    {
        if ($value) {
            $route = BusRoute::with('stops.location')->find($value);
            if ($route) {
                $this->form->stops = $route->stops->map(fn($s) => [
                    'route_stop_id' => $s->id,
                    'location_name' => $s->location->name,
                    'estimated_time' => '',
                    'status' => 'pending',
                ])->toArray();
            }
        } else {
            $this->form->stops = [];
        }
    }

    public function addCrew()
    {
        $this->form->crews[] = [
            'crew_id' => '',
            'assigned_position_id' => '',
            'boarding_location_id' => '',
            'dropoff_location_id' => '',
        ];
    }

    public function removeCrew($index)
    {
        unset($this->form->crews[$index]);
        $this->form->crews = array_values($this->form->crews);
    }

    #[Computed]
    public function routes()
    {
        return BusRoute::orderBy('name')->get();
    }

    #[Computed]
    public function buses()
    {
        return Bus::orderBy('name')->get();
    }

    #[Computed]
    public function crews()
    {
        return Crew::orderBy('name')->get();
    }

    #[Computed]
    public function positions()
    {
        return CrewPosition::orderBy('name')->get();
    }

    #[Computed]
    public function locations()
    {
        return Location::orderBy('name', 'asc')->get();
    }

    public function saveSchedule()
    {
        $this->form->store();
        session()->flash('success', 'Jadwal baru berhasil terbit.');
        return $this->redirect(route('schedules.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pages.schedules.create');
    }
}
