<?php

namespace App\Livewire\Pages\Bookings;

use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\BookingTicket;
use App\Models\Location;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Create extends Component
{
    // Step tracking
    public int $step = 1; // 1: Search, 2: Select Schedule, 3: Select Seats, 4: Passenger Info, 5: Confirmation

    // Search fields
    public ?int $origin_id = null;

    public ?int $destination_id = null;

    public $departure_date;

    // Selection fields
    public ?int $selected_schedule_id = null;

    public array $selected_seats = []; // Array of seat numbers

    public array $passengers = []; // [seat_number => ['name' => '', 'price' => 0]]

    // Customer fields
    public string $customer_name = '';

    public string $customer_phone = '';

    public function mount()
    {
        $this->departure_date = now()->format('Y-m-d');
    }

    #[Computed]
    public function locations()
    {
        return Location::orderBy('name', 'asc')->get();
    }

    #[Computed]
    public function schedules()
    {
        if (! $this->origin_id || ! $this->destination_id || ! $this->departure_date) {
            return collect();
        }

        // Filter schedules by date and if the route contains both origin and destination
        // For simplicity in this demo, we check if the route's origin/destination match
        // Or if they are in the route stops.
        return Schedule::query()
            ->whereDate('departure_date', '=', $this->departure_date, 'and')
            ->whereHas('route', function ($q) {
                $q->where(function ($q2) {
                    $q2->where('origin_location_id', $this->origin_id)
                        ->where('destination_location_id', $this->destination_id);
                })->orWhere(function ($q2) {
                    // Also check if both locations exist in route stops
                    $q2->whereHas('stops', fn ($s) => $s->where('location_id', $this->origin_id))
                        ->whereHas('stops', fn ($s) => $s->where('location_id', $this->destination_id));
                });
            })
            ->with(['route.origin', 'route.destination', 'bus.busClass'])
            ->get()
            ->filter(function ($schedule) {
                // Ensure origin comes before destination in the stops sequence
                $stops = $schedule->route->stops()->orderBy('stop_order')->get();
                $originIndex = $stops->search(fn ($s) => $s->location_id == $this->origin_id);
                $destIndex = $stops->search(fn ($s) => $s->location_id == $this->destination_id);

                // If it's a direct route (no stops matching but overall origin/dest match)
                if ($originIndex === false && $schedule->route->origin_location_id == $this->origin_id) {
                    $originIndex = -1;
                }
                if ($destIndex === false && $schedule->route->destination_location_id == $this->destination_id) {
                    $destIndex = 999;
                }

                return $originIndex !== false && $destIndex !== false && $originIndex < $destIndex;
            });
    }

    #[Computed]
    public function selectedSchedule()
    {
        return $this->selected_schedule_id ? Schedule::with(['bus.seatLayout', 'bus.busClass'])->find($this->selected_schedule_id) : null;
    }

    #[Computed]
    public function takenSeats()
    {
        if (! $this->selected_schedule_id) {
            return [];
        }

        return BookingTicket::whereHas('booking', function ($q) {
            $q->where('schedule_id', $this->selected_schedule_id)
                ->whereIn('payment_status', [PaymentStatus::PAID, PaymentStatus::UNPAID]);
        })->pluck('seat_number')->toArray();
    }

    public function selectSchedule($id)
    {
        $this->selected_schedule_id = $id;
        $this->step = 3;
        $this->selected_seats = [];
    }

    public function swapLocations()
    {
        $temp = $this->origin_id;
        $this->origin_id = $this->destination_id;
        $this->destination_id = $temp;
    }

    public function toggleSeat($seatNumber)
    {
        if (in_array($seatNumber, $this->takenSeats())) {
            return;
        }

        if (in_array($seatNumber, $this->selected_seats)) {
            $this->selected_seats = array_diff($this->selected_seats, [$seatNumber]);
            unset($this->passengers[$seatNumber]);
        } else {
            $this->selected_seats[] = $seatNumber;
            $this->passengers[$seatNumber] = ['name' => '', 'price' => $this->selectedSchedule->base_price];
        }
    }

    public function goToStep4()
    {
        if (empty($this->selected_seats)) {
            $this->dispatch('notify', type: 'error', title: 'Perhatian', message: 'Silakan pilih setidaknya satu kursi.');

            return;
        }
        $this->step = 4;
    }

    public function saveBooking()
    {
        $this->validate([
            'customer_name' => 'required|string|min:3',
            'customer_phone' => 'required|string|min:10',
            'passengers.*.name' => 'required|string|min:3',
        ]);

        try {
            DB::transaction(function () {
                $booking = Booking::create([
                    'booking_code' => 'BK-'.strtoupper(Str::random(8)),
                    'customer_name' => $this->customer_name,
                    'customer_phone' => $this->customer_phone,
                    'schedule_id' => $this->selected_schedule_id,
                    'boarding_location_id' => $this->origin_id,
                    'dropoff_location_id' => $this->destination_id,
                    'total_seats' => count($this->selected_seats),
                    'total_amount' => collect($this->passengers)->sum('price'),
                    'payment_status' => PaymentStatus::UNPAID,
                    'expired_at' => now()->addHours(2),
                ]);

                foreach ($this->passengers as $seatNumber => $data) {
                    BookingTicket::create([
                        'booking_id' => $booking->id,
                        'seat_number' => $seatNumber,
                        'passenger_name' => $data['name'],
                        'ticket_price' => $data['price'],
                    ]);
                }

                $this->redirect(route('bookings.show', $booking->id), navigate: true);
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', type: 'error', title: 'Gagal', message: $e->validator->errors()->first());
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: 'Gagal', message: 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.bookings.create');
    }
}

