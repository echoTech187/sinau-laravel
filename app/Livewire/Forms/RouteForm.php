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
    public $origin_agent_id = '';
    public $destination_agent_id = '';
    public $distance_km = '';
    
    // Array to hold dynamic stops
    public array $stops = [];

    public function setRoute(Route $route)
    {
        $this->routeModel = $route;
        $this->route_code = $route->route_code;
        $this->name = $route->name;
        $this->origin_agent_id = $route->origin_agent_id;
        $this->destination_agent_id = $route->destination_agent_id;
        $this->distance_km = $route->distance_km;

        // Populate stops
        $this->stops = $route->stops()->orderBy('stop_order')->get()->map(function ($stop) {
            return [
                'id' => $stop->id,
                'agent_id' => $stop->agent_id,
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
            'origin_agent_id' => 'required|exists:agents,id|different:destination_agent_id',
            'destination_agent_id' => 'required|exists:agents,id',
            'distance_km' => 'nullable|integer|min:1',
            
            // Dynamic stops validation
            'stops' => 'array',
            'stops.*.agent_id' => 'required|exists:agents,id',
            'stops.*.type' => ['required', Rule::enum(StopType::class)],
            'stops.*.is_checkpoint' => 'boolean',
        ];
    }

    public function store()
    {
        $this->validate();

        DB::transaction(function () {
            $route = Route::create([
                'route_code' => $this->route_code,
                'name' => $this->name,
                'origin_agent_id' => $this->origin_agent_id,
                'destination_agent_id' => $this->destination_agent_id,
                'distance_km' => $this->distance_km,
            ]);

            foreach ($this->stops as $index => $stop) {
                RouteStop::create([
                    'route_id' => $route->id,
                    'agent_id' => $stop['agent_id'],
                    'stop_order' => $index + 1,
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
            $this->routeModel->update([
                'route_code' => $this->route_code,
                'name' => $this->name,
                'origin_agent_id' => $this->origin_agent_id,
                'destination_agent_id' => $this->destination_agent_id,
                'distance_km' => $this->distance_km,
            ]);

            $this->routeModel->stops()->delete();

            foreach ($this->stops as $index => $stop) {
                RouteStop::create([
                    'route_id' => $this->routeModel->id,
                    'agent_id' => $stop['agent_id'],
                    'stop_order' => $index + 1,
                    'type' => $stop['type'],
                    'is_checkpoint' => $stop['is_checkpoint'] ?? false,
                ]);
            }
        });
    }
    
    public function addStop()
    {
        $this->stops[] = [
            'agent_id' => '',
            'type' => 'both',
            'is_checkpoint' => false,
        ];
    }
    
    public function removeStop($index)
    {
        unset($this->stops[$index]);
        $this->stops = array_values($this->stops);
    }
}

