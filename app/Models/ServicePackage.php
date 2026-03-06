<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    protected $fillable = [
        'chassis_brand',
        'km_interval',
        'name',
        'is_major',
    ];

    /**
     * Get the rules associated with this package.
     */
    public function maintenanceRules()
    {
        return $this->belongsToMany(MaintenanceRule::class)
                    ->withTimestamps();
    }}
