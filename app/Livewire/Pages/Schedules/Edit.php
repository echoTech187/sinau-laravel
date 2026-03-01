<?php

namespace App\Livewire\Pages\Schedules;

use App\Livewire\Forms\ScheduleForm;
use App\Models\Schedule;
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
#[Title('Edit Jadwal')]
class Edit extends Component
{
    public ScheduleForm $form;
    public Schedule $schedule;

    public function mount(Schedule $schedule)
    {
        $this->schedule = $schedule;
        $this->form->setSchedule($schedule);
    }

    public function updatedFormRouteId($value)
    {
        // Warn: Changing route will reset stops
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
        return BusRoute::orderBy('name', 'asc')->get();
    }

    #[Computed]
    public function buses()
    {
        return Bus::orderBy('name', 'asc')->get();
    }

    #[Computed]
    public function crews()
    {
        return Crew::orderBy('name', 'asc')->get();
    }

    #[Computed]
    public function positions()
    {
        return CrewPosition::orderBy('name', 'asc')->get();
    }

    #[Computed]
    public function locations()
    {
        return Location::orderBy('name', 'asc')->get();
    }

    public function saveSchedule()
    {
        $this->form->update();
        session()->flash('success', 'Perubahan jadwal telah disimpan.');
        return $this->redirect(route('schedules.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pages.schedules.edit');
    }
}
