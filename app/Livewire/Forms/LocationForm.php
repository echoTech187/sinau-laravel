<?php

namespace App\Livewire\Forms;

use App\Models\Location;
use Livewire\Attributes\Rule;
use Livewire\Form;
use Illuminate\Validation\Rule as ValidationRule;

class LocationForm extends Form
{
    public ?Location $location = null;

    #[Rule('required|string|min:3|max:255')]
    public string $name = '';

    #[Rule('required|string|max:100')]
    public string $city = '';

    #[Rule('required|string|max:100')]
    public string $province = '';

    #[Rule('required|string')]
    public string $address = '';

    #[Rule('nullable|numeric')]
    public ?float $latitude = null;

    #[Rule('nullable|numeric')]
    public ?float $longitude = null;

    #[Rule('nullable|string|in:circular,polygon')]
    public ?string $geofence_type = null;

    #[Rule('nullable|integer|min:1')]
    public ?int $geofence_radius_meter = null;

    #[Rule('nullable|string')]
    public ?string $qr_code_gate = null;

    #[Rule('boolean')]
    public bool $has_maintenance_facility = false;

    public array $role_ids = [];

    public function setLocation(Location $location)
    {
        $this->location = $location;
        $this->name = $location->name;
        $this->city = $location->city;
        $this->province = $location->province;
        $this->address = $location->address;
        $this->latitude = $location->latitude;
        $this->longitude = $location->longitude;
        $this->geofence_type = $location->geofence_type->value ?? null;
        $this->geofence_radius_meter = $location->geofence_radius_meter;
        $this->qr_code_gate = $location->qr_code_gate;
        $this->has_maintenance_facility = $location->has_maintenance_facility;
        $this->role_ids = $location->roles()->pluck('location_roles.id')->toArray();
    }

    public function store()
    {
        $validated = $this->validate();
        
        $location = Location::create($validated);
        $location->roles()->sync($this->role_ids);

        $this->reset();
    }

    public function update()
    {
        $validated = $this->validate();
        
        $this->location->update($validated);
        $this->location->roles()->sync($this->role_ids);
    }
}
