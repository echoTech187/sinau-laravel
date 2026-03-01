<?php

namespace App\Livewire\Pages\Shipments;

use App\Models\Shipment;
use Livewire\Component;

class Show extends Component
{
    public Shipment $shipment;

    public function mount(Shipment $shipment)
    {
        $this->shipment = $shipment->load(['origin', 'destination', 'schedule.bus', 'booking']);
    }

    public function render()
    {
        return view('livewire.pages.shipments.show');
    }
}
