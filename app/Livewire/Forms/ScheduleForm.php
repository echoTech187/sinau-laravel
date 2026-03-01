<?php

namespace App\Livewire\Forms;

use App\Enums\ScheduleStatus;
use App\Enums\TripType;
use App\Enums\StopStatus;
use App\Models\Schedule;
use App\Models\ScheduleCrew;
use App\Models\ScheduleStop;
use App\Models\Route as BusRoute;
use Livewire\Form;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ScheduleForm extends Form
{
    public ?Schedule $schedule = null;

    #[Validate('required|exists:routes,id')]
    public $route_id = '';

    #[Validate('required|exists:buses,id')]
    public $bus_id = '';

    #[Validate('required|date|after_or_equal:today')]
    public $departure_date = '';

    #[Validate('required')]
    public $departure_time = '';

    #[Validate('required|after:departure_time')]
    public $arrival_estimate = '';

    #[Validate('required|numeric|min:0')]
    public $base_price = 0;

    public $start_odometer = '';
    public $end_odometer = '';

    #[Validate('required')]
    public $trip_type = 'revenue';

    #[Validate('required')]
    public $status = 'scheduled';

    // Nested relations
    public $crews = [];
    public $stops = [];

    public function setSchedule(Schedule $schedule)
    {
        $this->schedule = $schedule;
        $this->route_id = $schedule->route_id;
        $this->bus_id = $schedule->bus_id;
        $this->departure_date = $schedule->departure_date->format('Y-m-d');
        $this->departure_time = $schedule->departure_time->format('Y-m-d\TH:i');
        $this->arrival_estimate = $schedule->arrival_estimate->format('Y-m-d\TH:i');
        $this->base_price = $schedule->base_price;
        $this->start_odometer = $schedule->start_odometer;
        $this->end_odometer = $schedule->end_odometer;
        $this->trip_type = $schedule->trip_type->value;
        $this->status = $schedule->status->value;

        $this->crews = $schedule->crews->map(fn($c) => [
            'crew_id' => $c->crew_id,
            'assigned_position_id' => $c->assigned_position_id,
            'boarding_location_id' => $c->boarding_location_id,
            'dropoff_location_id' => $c->dropoff_location_id,
        ])->toArray();

        $this->stops = $schedule->scheduleStops->map(fn($s) => [
            'id' => $s->id,
            'route_stop_id' => $s->route_stop_id,
            'location_name' => $s->routeStop->location->name ?? 'Unknown',
            'estimated_time' => $s->estimated_time,
            'status' => $s->status->value,
            'notes' => $s->notes,
        ])->toArray();
    }

    public function rules()
    {
        return [
            'route_id' => 'required|exists:routes,id',
            'bus_id' => 'required|exists:buses,id',
            'departure_date' => 'required|date',
            'departure_time' => 'required',
            'arrival_estimate' => 'required',
            'base_price' => 'required|numeric|min:0',
            'trip_type' => ['required', Rule::enum(TripType::class)],
            'status' => ['required', Rule::enum(ScheduleStatus::class)],
            'crews.*.crew_id' => 'required|exists:crews,id',
            'crews.*.assigned_position_id' => 'required|exists:crew_positions,id',
            'crews.*.boarding_location_id' => 'required|exists:locations,id',
            'crews.*.dropoff_location_id' => 'required|exists:locations,id',
            'stops.*.route_stop_id' => 'required|exists:route_stops,id',
            'stops.*.estimated_time' => 'required',
        ];
    }

    public function store()
    {
        $this->validate();

        return DB::transaction(function () {
            $schedule = Schedule::create($this->except(['schedule', 'crews', 'stops']));

            foreach ($this->crews as $crewData) {
                $schedule->crews()->create($crewData);
            }

            foreach ($this->stops as $stopData) {
                $schedule->scheduleStops()->create([
                    'route_stop_id' => $stopData['route_stop_id'],
                    'estimated_time' => $stopData['estimated_time'],
                    'status' => StopStatus::PENDING,
                ]);
            }

            return $schedule;
        });
    }

    public function update()
    {
        $this->validate();

        return DB::transaction(function () {
            $this->schedule->update($this->except(['schedule', 'crews', 'stops']));

            // Simple replace strategy for crews and stops
            $this->schedule->crews()->delete();
            foreach ($this->crews as $crewData) {
                $this->schedule->crews()->create($crewData);
            }

            $this->schedule->scheduleStops()->delete();
            foreach ($this->stops as $stopData) {
                $this->schedule->scheduleStops()->create([
                    'route_stop_id' => $stopData['route_stop_id'],
                    'estimated_time' => $stopData['estimated_time'],
                    'status' => $stopData['status'] ?? StopStatus::PENDING,
                    'notes' => $stopData['notes'] ?? null,
                ]);
            }

            return $this->schedule;
        });
    }
}
