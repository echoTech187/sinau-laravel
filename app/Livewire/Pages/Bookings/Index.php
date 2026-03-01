<?php

namespace App\Livewire\Pages\Bookings;

use App\Enums\PaymentStatus;
use App\Models\Booking;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    #[Computed]
    public function bookings()
    {
        return Booking::query()
            ->with(['schedule.route.origin', 'schedule.route.destination', 'schedule.bus.busClass'])
            ->when($this->search, function ($q) {
                $q->where('booking_code', 'like', '%'.$this->search.'%')
                    ->orWhere('customer_name', 'like', '%'.$this->search.'%')
                    ->orWhere('customer_phone', 'like', '%'.$this->search.'%');
            })
            ->when($this->status, function ($q) {
                $q->where('payment_status', $this->status);
            })
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function stats()
    {
        return [
            'total' => Booking::count('id'),
            'paid' => Booking::where('payment_status', '=', PaymentStatus::PAID, 'and')->count(),
            'unpaid' => Booking::where('payment_status', '=', PaymentStatus::UNPAID, 'and')->count(),
            'total_revenue' => Booking::where('payment_status', '=', PaymentStatus::PAID, 'and')->sum('total_amount'),
        ];
    }

    public function render()
    {
        return view('livewire.pages.bookings.index');
    }
}
