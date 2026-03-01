<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\RedemptionType;

class TicketRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_ticket_id', 'location_id', 'redeemed_at', 'type'
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
        'type' => RedemptionType::class,
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(BookingTicket::class, 'booking_ticket_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
