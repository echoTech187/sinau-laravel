<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\PaymentStatus;

class Booking extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'booking_code', 'customer_name', 'customer_phone', 'schedule_id',
        'boarding_location_id', 'dropoff_location_id', 'agent_id',
        'total_seats', 'total_amount', 'payment_method', 'expired_at', 'payment_status'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'expired_at' => 'datetime',
        'payment_status' => PaymentStatus::class,
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function boardingLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'boarding_location_id');
    }

    public function dropoffLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'dropoff_location_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(BookingTicket::class);
    }
}
