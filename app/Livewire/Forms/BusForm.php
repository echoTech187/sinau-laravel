<?php

namespace App\Livewire\Forms;

use App\Models\Bus;
use App\Services\ImageService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class BusForm extends Form
{
    use WithFileUploads;

    public ?Bus $bus = null;

    public $bus_class_id;
    public $seat_layout_id;
    public $base_pool_id;
    public $fleet_code;
    public $plate_number;
    public $rfid_tag_id;
    public $name;
    public $chassis_brand;
    public $chassis_type;
    public $body_maker;
    public $body_model;
    public $manufacture_year;
    public $engine_number;
    public $chassis_number;
    public $total_seats;
    public $max_baggage_weight_kg;
    public $max_baggage_volume_m3;
    public $stnk_expired_at;
    public $kir_expired_at;
    public $kps_expired_at;
    public $insurance_expired_at;
    public $current_odometer;
    public $average_daily_km;
    public $status = 'active';

    public $photo;
    public $photo_path;

    public function rules()
    {
        return [
            'bus_class_id' => 'required|exists:bus_classes,id',
            'seat_layout_id' => 'required|exists:seat_layouts,id',
            'base_pool_id' => 'required|exists:locations,id',
            'fleet_code' => ['required', 'string', 'max:255', Rule::unique('buses', 'fleet_code')->ignore($this->bus)],
            'plate_number' => ['required', 'string', 'max:255', Rule::unique('buses', 'plate_number')->ignore($this->bus)],
            'rfid_tag_id' => ['nullable', 'string', 'max:255', Rule::unique('buses', 'rfid_tag_id')->ignore($this->bus)],
            'name' => 'nullable|string|min:3|max:255',
            'chassis_brand' => 'required|string|max:255',
            'chassis_type' => 'required|string|max:255',
            'body_maker' => 'required|string|max:255',
            'body_model' => 'required|string|max:255',
            'manufacture_year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'engine_number' => ['required', 'string', 'max:255', Rule::unique('buses', 'engine_number')->ignore($this->bus)],
            'chassis_number' => ['required', 'string', 'max:255', Rule::unique('buses', 'chassis_number')->ignore($this->bus)],
            'total_seats' => 'required|integer|min:1',
            'max_baggage_weight_kg' => 'required|integer|min:0',
            'max_baggage_volume_m3' => 'nullable|numeric|min:0',
            'stnk_expired_at' => 'required|date',
            'kir_expired_at' => 'required|date',
            'kps_expired_at' => 'required|date',
            'insurance_expired_at' => 'required|date',
            'current_odometer' => 'required|integer|min:0',
            'average_daily_km' => 'required|integer|min:0',
            'status' => 'required|string|in:active,maintenance,inactive',
            'photo' => 'nullable|image|max:2048',
        ];
    }

    public function setBus(Bus $bus)
    {
        $this->bus = $bus;
        $this->bus_class_id = $bus->bus_class_id;
        $this->seat_layout_id = $bus->seat_layout_id;
        $this->base_pool_id = $bus->base_pool_id;
        $this->fleet_code = $bus->fleet_code;
        $this->plate_number = $bus->plate_number;
        $this->rfid_tag_id = $bus->rfid_tag_id;
        $this->name = $bus->name;
        $this->chassis_brand = $bus->chassis_brand;
        $this->chassis_type = $bus->chassis_type;
        $this->body_maker = $bus->body_maker;
        $this->body_model = $bus->body_model;
        $this->manufacture_year = $bus->manufacture_year;
        $this->engine_number = $bus->engine_number;
        $this->chassis_number = $bus->chassis_number;
        $this->total_seats = $bus->total_seats;
        $this->max_baggage_weight_kg = $bus->max_baggage_weight_kg;
        $this->max_baggage_volume_m3 = $bus->max_baggage_volume_m3;
        
        // Format dates for input type date
        $this->stnk_expired_at = $bus->stnk_expired_at ? date('Y-m-d', strtotime($bus->stnk_expired_at)) : null;
        $this->kir_expired_at = $bus->kir_expired_at ? date('Y-m-d', strtotime($bus->kir_expired_at)) : null;
        $this->kps_expired_at = $bus->kps_expired_at ? date('Y-m-d', strtotime($bus->kps_expired_at)) : null;
        $this->insurance_expired_at = $bus->insurance_expired_at ? date('Y-m-d', strtotime($bus->insurance_expired_at)) : null;
        
        $this->current_odometer = $bus->current_odometer;
        $this->average_daily_km = $bus->average_daily_km;
        $this->status = $bus->status->value ?? $bus->status;
        $this->photo_path = $bus->photo_path;
    }

    public function store()
    {
        $validated = $this->validate();
        
        if ($this->photo) {
            $validated['photo_path'] = $this->processPhoto($this->photo);
        }
        
        unset($validated['photo']);

        Bus::create($validated);
        $this->reset();
    }

    public function update()
    {
        $validated = $this->validate();
        
        if ($this->photo) {
            // Delete old photo before replacing
            if ($this->bus->photo_path) {
                Storage::disk('public')->delete($this->bus->photo_path);
            }
            $validated['photo_path'] = $this->processPhoto($this->photo);
        }
        
        unset($validated['photo']);

        $this->bus->update($validated);
        $this->reset();
    }

    /**
     * Crop to 7:5 aspect ratio (1400x1000px) and compress to JPEG.
     */
    private function processPhoto($upload): string
    {
        return app(ImageService::class)->cropAndCompress(
            upload: $upload,
            directory: 'buses/photos',
            disk: 'public',
            width: 1400,
            height: 1000,
            quality: 82
        );
    }
}
