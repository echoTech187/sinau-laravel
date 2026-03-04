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

    #[Validate('nullable|exists:buses,id')]
    public $bus_id = null;

    #[Validate('required|date|after_or_equal:today')]
    public $departure_date = '';

    #[Validate('required')]
    public $departure_time = '';

    #[Validate('required|after:departure_time')]
    public $arrival_estimate = '';

    #[Validate('required|numeric|min:0')]
    public $base_price = 0;

    #[Validate('nullable|integer|min:0')]
    public $start_odometer = null;

    #[Validate('nullable|integer|min:0|gte:start_odometer')]
    public $end_odometer = null;

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
        $this->base_price = (int) $schedule->base_price;
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
            'location_name' => $s->routeStop->agent->name ?? 'Unknown',
            'estimated_time' => $s->estimated_time,
            'status' => $s->status->value,
            'notes' => $s->notes,
        ])->toArray();
    }

    public function rules()
    {
        return [
            'route_id' => 'required|exists:routes,id',
            'bus_id' => 'nullable|exists:buses,id',
            'departure_date' => 'required|date',
            'departure_time' => 'required',
            'arrival_estimate' => 'required',
            'base_price' => 'required|numeric|min:0',
            'start_odometer' => 'nullable|integer|min:0',
            'end_odometer' => 'nullable|integer|min:0|gte:start_odometer',
            'trip_type' => ['required', Rule::enum(TripType::class)],
            'status' => ['required', Rule::enum(ScheduleStatus::class)],
            'crews.*.crew_id' => 'required|exists:crews,id',
            'crews.*.assigned_position_id' => 'required|exists:crew_positions,id',
            'crews.*.boarding_location_id' => 'required|exists:agents,id',
            'crews.*.dropoff_location_id'   => 'required|exists:agents,id',
            'stops.*.route_stop_id' => 'required|exists:route_stops,id',
            'stops.*.estimated_time' => 'required',
        ];
    }

    public function store()
    {
        $this->validate();

        return DB::transaction(function () {
            // Normalize empty optional integers to null
            $data = $this->except(['schedule', 'crews', 'stops']);
            $data['bus_id']         = $this->bus_id !== '' ? $this->bus_id : null;
            $data['start_odometer'] = $this->start_odometer !== '' ? $this->start_odometer : null;
            $data['end_odometer']   = $this->end_odometer   !== '' ? $this->end_odometer   : null;

            $schedule = Schedule::create($data);

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
            // Normalize empty optional integers to null
            $data = $this->except(['schedule', 'crews', 'stops']);
            $data['bus_id']         = $this->bus_id !== '' ? $this->bus_id : null;
            $data['start_odometer'] = $this->start_odometer !== '' ? $this->start_odometer : null;
            $data['end_odometer']   = $this->end_odometer   !== '' ? $this->end_odometer   : null;

            $this->schedule->update($data);

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
