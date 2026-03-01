<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ApprovalStatus;

class ManifestApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'manifest_id', 'category_id', 'approved_by_id', 
        'achieved_percentage', 'status'
    ];

    protected $casts = [
        'achieved_percentage' => 'decimal:2',
        'status' => ApprovalStatus::class,
    ];

    public function manifest(): BelongsTo
    {
        return $this->belongsTo(OperationalManifest::class, 'manifest_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InspectionCategory::class);
    }
}
