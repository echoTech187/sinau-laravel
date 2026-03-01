<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\StopStatus;
use App\Enums\DetectionMethod;

class ScheduleStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id', 'route_stop_id', 'estimated_time',
        'actual_time', 'notes', 'status', 'detection_method'
    ];

    protected $casts = [
        'status' => StopStatus::class,
        'detection_method' => DetectionMethod::class,
        'actual_time' => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function routeStop(): BelongsTo
    {
        return $this->belongsTo(RouteStop::class);
    }
}
