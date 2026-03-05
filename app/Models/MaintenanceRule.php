<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_name', 'chassis_brand', 'interval_km', 'tolerance_km', 
        'estimated_hours', 'preferred_agent_id'
    ];

    public function preferredAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'preferred_agent_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }
}
