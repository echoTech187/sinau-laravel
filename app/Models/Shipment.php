<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\ShipmentStatus;

class Shipment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'waybill_number', 'barcode_number', 'schedule_id', 'booking_id',
        'origin_location_id', 'destination_location_id', 'sender_name',
        'sender_phone', 'receiver_name', 'receiver_phone', 'item_description',
        'actual_weight_kg', 'chargeable_weight_kg', 'shipping_cost',
        'created_by_user_id', 'status'
    ];

    protected $casts = [
        'actual_weight_kg' => 'decimal:2',
        'chargeable_weight_kg' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'status' => ShipmentStatus::class,
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function origin(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_location_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }
}
