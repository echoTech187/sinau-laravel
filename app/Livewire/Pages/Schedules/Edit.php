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
#[Title('Edit Jadwal')]
class Edit extends Component
{
    public ScheduleForm $form;
    public Schedule $schedule;

    /** Total km of selected route for arrival estimate */
    public float $totalRouteKm = 0;

    public string $routeOriginName      = '';
    public string $routeDestinationName = '';

    public function mount(Schedule $schedule)
    {
        $this->schedule = $schedule;
        $this->form->setSchedule($schedule);

        // Populate initial route info without recalculating stops
        $route = BusRoute::with('origin.location', 'destination.location')->find($schedule->route_id);
        if ($route) {
            $this->totalRouteKm         = (float) ($route->distance_km ?? 0);
            $this->routeOriginName      = $route->origin?->name ?? '';
            $this->routeDestinationName = $route->destination?->name ?? '';
        }
    }

    public function updatedFormRouteId($value)
    {
        if ($value) {
            $route = BusRoute::with('stops.agent.location', 'origin.location', 'destination.location')->find($value);
            if ($route) {
                $this->totalRouteKm        = (float) ($route->distance_km ?? 0);
                $this->routeOriginName      = $route->origin?->name ?? '';
                $this->routeDestinationName = $route->destination?->name ?? '';
                $prevLat = $route->origin?->location?->latitude;
                $prevLon = $route->origin?->location?->longitude;
                $cumKm   = 0;

                $this->form->stops = $route->stops->map(function ($s) use (&$prevLat, &$prevLon, &$cumKm) {
                    $lat = $s->agent?->location?->latitude;
                    $lon = $s->agent?->location?->longitude;

                    if ($prevLat && $prevLon && $lat && $lon) {
                        $cumKm += $this->haversineKm($prevLat, $prevLon, $lat, $lon) * 1.35;
                    }

                    $prevLat = $lat;
                    $prevLon = $lon;

                    return [
                        'route_stop_id'  => $s->id,
                        'location_name'  => $s->agent?->name ?? 'Unknown',
                        'estimated_time' => '',
                        'km_from_start'  => round($cumKm, 2),
                        'status'         => 'pending',
                    ];
                })->toArray();

                $this->calculateStopEtas();
            }
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

    private function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a    = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public function addCrew()
    {
        $this->form->crews[] = [
            'crew_id'              => '',
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
        return Agent::orderBy('name', 'asc')->get();
    }

    public function saveSchedule()
    {
        try {
            $this->form->update();
            $this->dispatch('notify', ['type' => 'success', 'title' => 'Berhasil', 'message' => 'Jadwal operasional berhasil diperbarui.']);

            return $this->redirect(route('schedules.index'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Gagal', 'message' => $e->validator->errors()->first()]);

            return;
        }
    }

    public function render()
    {
        return view('livewire.pages.schedules.edit');
    }
}
