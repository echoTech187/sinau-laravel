<?php

namespace App\Livewire\Forms;

use App\Models\Shipment;
use App\Enums\ShipmentStatus;
use Livewire\Form;
use Livewire\Attributes\Rule;
use Illuminate\Support\Str;

class ShipmentForm extends Form
{
    public ?Shipment $shipment = null;

    #[Rule('required|string|min:3|max:255')]
    public string $sender_name = '';

    #[Rule('required|string|min:10|max:20')]
    public string $sender_phone = '';

    #[Rule('required|string|min:3|max:255')]
    public string $receiver_name = '';

    #[Rule('required|string|min:10|max:20')]
    public string $receiver_phone = '';

    #[Rule('required|string|min:5|max:1000')]
    public string $item_description = '';

    #[Rule('required|numeric|min:0.1')]
    public float $actual_weight_kg = 0;

    #[Rule('required|numeric|min:0')]
    public float $chargeable_weight_kg = 0;

    #[Rule('required|numeric|min:0')]
    public float $shipping_cost = 0;

    #[Rule('required|exists:locations,id')]
    public ?int $origin_location_id = null;

    #[Rule('required|exists:locations,id')]
    public ?int $destination_location_id = null;

    #[Rule('nullable|exists:schedules,id')]
    public ?int $schedule_id = null;

    #[Rule('nullable|exists:bookings,id')] // UUID
    public ?string $booking_id = null;

    public function setShipment(Shipment $shipment)
    {
        $this->shipment = $shipment;
        $this->sender_name = $shipment->sender_name;
        $this->sender_phone = $shipment->sender_phone;
        $this->receiver_name = $shipment->receiver_name;
        $this->receiver_phone = $shipment->receiver_phone;
        $this->item_description = $shipment->item_description;
        $this->actual_weight_kg = (float) $shipment->actual_weight_kg;
        $this->chargeable_weight_kg = (float) $shipment->chargeable_weight_kg;
        $this->shipping_cost = (float) $shipment->shipping_cost;
        $this->origin_location_id = $shipment->origin_location_id;
        $this->destination_location_id = $shipment->destination_location_id;
        $this->schedule_id = $shipment->schedule_id;
        $this->booking_id = $shipment->booking_id;
    }

    public function store()
    {
        $this->validate();

        $shipment = Shipment::create([
            'waybill_number' => 'WB-' . strtoupper(Str::random(10)),
            'barcode_number' => 'QR-' . strtoupper(Str::random(8)),
            'sender_name' => $this->sender_name,
            'sender_phone' => $this->sender_phone,
            'receiver_name' => $this->receiver_name,
            'receiver_phone' => $this->receiver_phone,
            'item_description' => $this->item_description,
            'actual_weight_kg' => $this->actual_weight_kg,
            'chargeable_weight_kg' => $this->chargeable_weight_kg,
            'shipping_cost' => $this->shipping_cost,
            'origin_location_id' => $this->origin_location_id,
            'destination_location_id' => $this->destination_location_id,
            'schedule_id' => $this->schedule_id,
            'booking_id' => $this->booking_id,
            'created_by_user_id' => auth()->id(),
            'status' => ShipmentStatus::RECEIVED_AT_AGENT,
        ]);

        $this->reset();
        return $shipment;
    }

    public function update()
    {
        $this->validate();

        $this->shipment->update($this->all());
    }
}
