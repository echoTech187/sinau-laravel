<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleCrew extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id', 'crew_id', 'assigned_position_id',
        'boarding_location_id', 'dropoff_location_id'
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function crew(): BelongsTo
    {
        return $this->belongsTo(Crew::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(CrewPosition::class, 'assigned_position_id');
    }

    public function boardingLocation(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'boarding_location_id');
    }

    public function dropoffLocation(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'dropoff_location_id');
    }
}
