<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\TripType;
use App\Enums\ScheduleStatus;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'route_id', 'bus_id', 'departure_date', 'departure_time',
        'arrival_estimate', 'base_price', 'start_odometer', 'end_odometer',
        'trip_type', 'status'
    ];

    protected $casts = [
        'departure_date' => 'date',
        'departure_time' => 'datetime',
        'arrival_estimate' => 'datetime',
        'base_price' => 'decimal:2',
        'trip_type' => TripType::class,
        'status' => ScheduleStatus::class,
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function crews(): HasMany
    {
        return $this->hasMany(ScheduleCrew::class);
    }

    public function breadcrumbs(): HasMany
    {
        return $this->hasMany(BusBreadcrumb::class);
    }

    public function scheduleStops(): HasMany
    {
        return $this->hasMany(ScheduleStop::class);
    }

    public function manifests(): HasMany
    {
        return $this->hasMany(OperationalManifest::class);
    }
}
