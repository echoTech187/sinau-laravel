<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Agent;
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
                'distance_km' => $this->distance_km === '' ? null : $this->distance_km,
            ]);

            $stopsWithDistance = $this->calculateDistances();

            foreach ($stopsWithDistance as $index => $stop) {
                RouteStop::create([
                    'route_id' => $route->id,
                    'agent_id' => $stop['agent_id'],
                    'stop_order' => $index + 1,
                    'type' => $stop['type'],
                    'is_checkpoint' => $stop['is_checkpoint'] ?? false,
                    'distance_from_origin_km' => $stop['distance_from_origin_km'],
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
                'distance_km' => $this->distance_km === '' ? null : $this->distance_km,
            ]);

            $this->routeModel->stops()->delete();

            $stopsWithDistance = $this->calculateDistances();

            foreach ($stopsWithDistance as $index => $stop) {
                RouteStop::create([
                    'route_id' => $this->routeModel->id,
                    'agent_id' => $stop['agent_id'],
                    'stop_order' => $index + 1,
                    'type' => $stop['type'],
                    'is_checkpoint' => $stop['is_checkpoint'] ?? false,
                    'distance_from_origin_km' => $stop['distance_from_origin_km'],
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

    private function calculateDistances(): array
    {
        $originAgent = Agent::with('location')->find($this->origin_agent_id);
        if (!$originAgent || !$originAgent->location) {
            return array_map(fn($s) => array_merge($s, ['distance_from_origin_km' => null]), $this->stops);
        }

        $prevLat = (float) $originAgent->location->latitude;
        $prevLon = (float) $originAgent->location->longitude;
        $cumulativeKm = 0;

        $results = [];
        foreach ($this->stops as $stop) {
            $agent = Agent::with('location')->find($stop['agent_id']);
            if ($agent && $agent->location && $agent->location->latitude && $agent->location->longitude) {
                $lat = (float) $agent->location->latitude;
                $lon = (float) $agent->location->longitude;
                // Add distance with 1.35 factor (for road winding adjustment as seen in Schedule component)
                $segmentKm = $this->haversineKm($prevLat, $prevLon, $lat, $lon) * 1.35;
                $cumulativeKm += $segmentKm;
                
                $prevLat = $lat;
                $prevLon = $lon;
            }
            
            $results[] = array_merge($stop, ['distance_from_origin_km' => round($cumulativeKm, 2)]);
        }

        return $results;
    }

    private function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a    = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}

