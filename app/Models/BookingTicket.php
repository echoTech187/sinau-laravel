<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'seat_number', 'passenger_name', 'ticket_price',
        'last_scanned_location_id', 'last_scanned_at'
    ];

    protected $casts = [
        'ticket_price' => 'decimal:2',
        'last_scanned_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function lastScannedLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'last_scanned_location_id');
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(TicketRedemption::class);
    }
}
