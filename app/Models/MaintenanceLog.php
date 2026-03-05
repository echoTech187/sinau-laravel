<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\MaintenanceType;
use App\Enums\MaintenanceStatus;

class MaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id', 'maintenance_rule_id', 'location_id', 'reported_by_id',
        'issue_description', 'total_cost', 'vendor_name', 'type', 'status', 'odometer_at_service',
        'next_estimated_date', 'resolved_at'
    ];

    protected $casts = [
        'type' => MaintenanceType::class,
        'status' => MaintenanceStatus::class,
        'total_cost' => 'decimal:2',
        'next_estimated_date' => 'date',
        'resolved_at' => 'datetime',
    ];

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRule::class, 'maintenance_rule_id');
    }
}
