<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\OperationalManifestStatus;

class OperationalManifest extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'schedule_id', 'manifest_number', 'issued_at', 
        'authorized_by_id', 'status'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'status' => OperationalManifestStatus::class,
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(ManifestChecklist::class, 'manifest_id');
    }

    public function authorizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ManifestApproval::class, 'manifest_id');
    }
}
