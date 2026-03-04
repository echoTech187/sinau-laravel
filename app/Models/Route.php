<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    use HasFactory;

    protected $fillable = ['route_code', 'name', 'origin_agent_id', 'destination_agent_id', 'distance_km'];

    public function origin(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'origin_agent_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'destination_agent_id');
    }

    public function stops(): HasMany
    {
        return $this->hasMany(RouteStop::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
