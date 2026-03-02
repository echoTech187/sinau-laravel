<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Route;
use App\Models\RouteStop;
use App\Enums\StopType;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class RouteForm extends Form
{
    public ?Route $routeModel = null;

    public $route_code = '';
    public $name = '';
    public $origin_location_id = '';
    public $destination_location_id = '';
    public $distance_km = '';
    
    // Array to hold dynamic stops
    public array $stops = [];

    public function setRoute(Route $route)
    {
        $this->routeModel = $route;
        $this->route_code = $route->route_code;
        $this->name = $route->name;
        $this->origin_location_id = $route->origin_location_id;
        $this->destination_location_id = $route->destination_location_id;
        $this->distance_km = $route->distance_km;

        // Populate stops
        $this->stops = $route->stops()->orderBy('stop_order')->get()->map(function ($stop) {
            return [
                'id' => $stop->id,
                'location_id' => $stop->location_id,
                'type' => $stop->type->value,
                'is_checkpoint' => $stop->is_checkpoint,
            ];
        })->toArray();
    }

    public function rules()
    {
        return [
            'route_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('routes', 'route_code')->ignore($this->routeModel?->id)
            ],
            'name' => 'required|string|min:3|max:255',
            'origin_location_id' => 'required|exists:locations,id|different:destination_location_id',
            'destination_location_id' => 'required|exists:locations,id',
            'distance_km' => 'nullable|integer|min:1',
            
            // Dynamic stops validation
            'stops' => 'array',
            'stops.*.location_id' => 'required|exists:locations,id',
            'stops.*.type' => ['required', Rule::enum(StopType::class)],
            'stops.*.is_checkpoint' => 'boolean',
        ];
    }

    public function store()
    {
        $this->validate();

        DB::transaction(function () {
            // Priority 1: Create Parent Route
            $route = Route::create([
                'route_code' => $this->route_code,
                'name' => $this->name,
                'origin_location_id' => $this->origin_location_id,
                'destination_location_id' => $this->destination_location_id,
                'distance_km' => $this->distance_km,
            ]);

            // Priority 2: Iteratively create RouteStops and assign logical ordering
            foreach ($this->stops as $index => $stop) {
                RouteStop::create([
                    'route_id' => $route->id,
                    'location_id' => $stop['location_id'],
                    'stop_order' => $index + 1, // 1-based ordering
                    'type' => $stop['type'],
                    'is_checkpoint' => $stop['is_checkpoint'] ?? false,
                ]);
            }
        });
    }

    public function update()
    {
        $this->validate();

        DB::transaction(function () {
            // Priority 1: Update Parent Route
            $this->routeModel->update([
                'route_code' => $this->route_code,
                'name' => $this->name,
                'origin_location_id' => $this->origin_location_id,
                'destination_location_id' => $this->destination_location_id,
                'distance_km' => $this->distance_km,
            ]);

            // Priority 2: Sync RouteStops. We wipe existing ones and recreate for pure state.
            $this->routeModel->stops()->delete();

            foreach ($this->stops as $index => $stop) {
                RouteStop::create([
                    'route_id' => $this->routeModel->id,
                    'location_id' => $stop['location_id'],
                    'stop_order' => $index + 1, // Resets sequential order based on UI index
                    'type' => $stop['type'],
                    'is_checkpoint' => $stop['is_checkpoint'] ?? false,
                ]);
            }
        });
    }
    
    public function addStop()
    {
        $this->stops[] = [
            'location_id' => '',
            'type' => 'both',
            'is_checkpoint' => false,
        ];
    }
    
    public function removeStop($index)
    {
        unset($this->stops[$index]);
        $this->stops = array_values($this->stops); // Re-index the array
    }
}
