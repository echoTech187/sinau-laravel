<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\BusStatus;

class Bus extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bus_class_id', 'seat_layout_id', 'base_pool_id', 'fleet_code', 
        'plate_number', 'rfid_tag_id', 'name', 'chassis_brand', 'chassis_type', 
        'body_maker', 'body_model', 'manufacture_year', 'engine_number', 
        'chassis_number', 'total_seats', 'max_baggage_weight_kg', 
        'max_baggage_volume_m3', 'stnk_expired_at', 'kir_expired_at', 
        'kps_expired_at', 'insurance_expired_at', 'current_odometer', 
        'average_daily_km', 'status'
    ];

    protected $casts = [
        'status' => BusStatus::class,
        'stnk_expired_at' => 'date',
        'kir_expired_at' => 'date',
        'kps_expired_at' => 'date',
        'insurance_expired_at' => 'date',
        'max_baggage_volume_m3' => 'decimal:2',
    ];

    public function busClass(): BelongsTo
    {
        return $this->belongsTo(BusClass::class);
    }

    public function seatLayout(): BelongsTo
    {
        return $this->belongsTo(SeatLayout::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(BusInventory::class);
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }
}
