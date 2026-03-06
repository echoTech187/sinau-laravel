<?php

namespace App\Livewire\Pages\Schedules;

use App\Livewire\Forms\ScheduleForm;
use App\Models\Agent;
use App\Models\Bus;
use App\Models\Crew;
use App\Models\CrewPosition;
use App\Models\Location;
use App\Models\Route as BusRoute;
use App\Models\Schedule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Buat Jadwal Baru')]
class Create extends Component
{
    public ScheduleForm $form;

    /** Total km of selected route for arrival estimate */
    public float $totalRouteKm = 0;

    public string $routeOriginName      = '';
    public string $routeDestinationName = '';

    public function mount()
    {
        $this->form->departure_date = now()->format('Y-m-d');
    }

    public function updatedFormRouteId($value)
    {
        if ($value) {
            $route = BusRoute::with('stops.agent.location', 'origin.location', 'destination.location')->find($value);
            if ($route) {
                $this->totalRouteKm        = (float) ($route->distance_km ?? 0);
                $this->routeOriginName      = $route->origin?->name ?? '';
                $this->routeDestinationName = $route->destination?->name ?? '';

                $this->form->stops = $route->stops->map(function ($s) {
                    return [
                        'route_stop_id' => $s->id,
                        'location_name' => $s->agent?->name ?? 'Unknown',
                        'estimated_time' => '',
                        'km_from_start'  => (float) ($s->distance_from_origin_km ?? 0),
                        'status'         => 'pending',
                    ];
                })->toArray();

                // Auto-fill ETAs if departure_time already set
                $this->calculateStopEtas();
            }
        } else {
            $this->form->stops = [];
        }
    }

    public function updatedFormDepartureTime($value)
    {
        $this->calculateStopEtas();
    }

    private function calculateStopEtas(): void
    {
        if (empty($this->form->departure_time) || empty($this->form->stops)) {
            return;
        }

        try {
            $departure = \Carbon\Carbon::parse($this->form->departure_time);
        } catch (\Exception) {
            return;
        }

        $this->form->stops = array_map(function ($stop) use ($departure) {
            $km  = (float) ($stop['km_from_start'] ?? 0);
            $eta = $departure->copy()->addMinutes((int) round($km / 60 * 60));
            $stop['estimated_time'] = $eta->format('H:i');
            return $stop;
        }, $this->form->stops);

        // Also fill arrival_estimate using total route distance
        if ($this->totalRouteKm > 0) {
            $arrival = $departure->copy()->addMinutes((int) round($this->totalRouteKm / 60 * 60));
            $this->form->arrival_estimate = $arrival->format('Y-m-d\TH:i');
        }
    }



    public function addCrew()
    {
        $this->form->crews[] = [
            'crew_id'             => '',
            'assigned_position_id' => '',
            'boarding_location_id' => '',
            'dropoff_location_id'  => '',
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
    public function agents()
    {
        return Agent::with('location')->get()->sortBy(function($agent) {
            return ($agent->location->province ?? '') . $agent->name;
        });
    }

    public function saveSchedule()
    {
        $this->authorize('create', Schedule::class);

        try {
            $this->form->store();
            $this->dispatch('notify', ['type' => 'success', 'title' => 'Berhasil', 'message' => 'Jadwal operasional baru berhasil disimpan.']);

            return $this->redirect(route('schedules.index'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Gagal', 'message' => $e->validator->errors()->first()]);

            return;
        }
    }

    public function render()
    {
        return view('livewire.pages.schedules.create');
    }
}
