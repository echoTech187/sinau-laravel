<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\StopType;

class RouteStop extends Model
{
    use HasFactory;

    protected $fillable = ['route_id', 'agent_id', 'stop_order', 'type', 'is_checkpoint'];

    protected $casts = [
        'type' => StopType::class,
        'is_checkpoint' => 'boolean',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function scheduleStops(): HasMany
    {
        return $this->hasMany(ScheduleStop::class);
    }
}
