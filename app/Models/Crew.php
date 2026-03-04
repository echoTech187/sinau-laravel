<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\CrewStatus;

class Crew extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nik', 'crew_position_id', 'employee_number', 'name', 
        'gender', 'birth_date', 'religion', 'marital_status', 
        'blood_type', 'original_address', 'current_address', 'domicile_city',
        'phone_number', 'contact_phone_1', 'contact_phone_2', 
        'license_number', 'license_expired_at', 'rank', 'spouse_name', 
        'children_count', 'join_date', 'education', 'status', 'region',
        'pool_id', 'agent_id', 'bus_id', 'route_id', 'photo_path'
    ];

    protected $casts = [
        'status' => CrewStatus::class,
        'license_expired_at' => 'date',
        'birth_date' => 'date',
        'join_date' => 'date',
        'children_count' => 'integer',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(CrewPosition::class, 'crew_position_id');
    }

    public function pool(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'pool_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
}
