<?php

namespace App\Livewire\Pages\Shipments;

use App\Models\Location;
use App\Models\Schedule;
use App\Models\Booking;
use App\Livewire\Forms\ShipmentForm;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Create extends Component
{
    public ShipmentForm $form;

    public function mount()
    {
        // Default values if needed
    }

    #[Computed]
    public function locations()
    {
        return Location::orderBy('name', 'asc')->get();
    }

    #[Computed]
    public function activeSchedules()
    {
        return Schedule::with(['route', 'bus'])
            ->where('status', '!=', 'cancelled')
            ->latest('departure_time')
            ->get();
    }

    #[Computed]
    public function recentBookings()
    {
        return Booking::latest('created_at')->take(50)->get();
    }

    public function save()
    {
        $shipment = $this->form->store();
        
        $this->dispatch('notify', 'Kargo berhasil didaftarkan.', 'success');
        $this->redirect(route('shipments.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pages.shipments.create');
    }
}
