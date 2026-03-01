<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\GeofenceType;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'city', 'province', 'address', 'latitude', 'longitude',
        'geofence_type', 'geofence_radius_meter', 'geofence_polygon_path',
        'qr_code_gate', 'has_maintenance_facility'
    ];

    protected $casts = [
        'geofence_type' => GeofenceType::class,
        'geofence_polygon_path' => 'array',
        'has_maintenance_facility' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(LocationRole::class, 'location_has_role');
    }

    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }
}
