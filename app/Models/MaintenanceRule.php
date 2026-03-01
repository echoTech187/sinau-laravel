<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceRule extends Model
{
    use HasFactory;

    protected $fillable = ['task_name', 'chassis_brand', 'interval_km', 'tolerance_km'];

    public function logs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }
}
