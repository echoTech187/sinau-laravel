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
        /** @var object|null $stats */
        $stats = Booking::toBase()
            ->selectRaw('COUNT(id) as total')
            ->selectRaw('SUM(CASE WHEN payment_status = ? THEN 1 ELSE 0 END) as paid', [PaymentStatus::PAID->value])
            ->selectRaw('SUM(CASE WHEN payment_status = ? THEN 1 ELSE 0 END) as unpaid', [PaymentStatus::UNPAID->value])
            ->selectRaw('SUM(CASE WHEN payment_status = ? THEN total_amount ELSE 0 END) as total_revenue', [PaymentStatus::PAID->value])
            ->first();

        return [
            'total' => $stats?->total ?? 0,
            'paid' => $stats?->paid ?? 0,
            'unpaid' => $stats?->unpaid ?? 0,
            'total_revenue' => $stats?->total_revenue ?? 0,
        ];
    }

    public function render()
    {
        return view('livewire.pages.bookings.index');
    }
}

