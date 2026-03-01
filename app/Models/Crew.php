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
        'crew_position_id', 'employee_number', 'name', 
        'phone_number', 'license_number', 'license_expired_at', 'status'
    ];

    protected $casts = [
        'status' => CrewStatus::class,
        'license_expired_at' => 'date',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(CrewPosition::class, 'crew_position_id');
    }
}
