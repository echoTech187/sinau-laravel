<?php

namespace App\Livewire\Pages\Bookings;

use App\Models\Booking;
use Livewire\Component;

class Show extends Component
{
    public Booking $booking;

    public function mount(Booking $booking)
    {
        $this->booking = $booking->load(['schedule.route.origin', 'schedule.route.destination', 'schedule.bus.busClass', 'tickets', 'boardingLocation', 'dropoffLocation']);
    }

    public function render()
    {
        return view('livewire.pages.bookings.show');
    }
}

