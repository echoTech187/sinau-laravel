<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ManifestResult;

class ManifestChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'manifest_id', 'inspection_item_id', 'checked_by_id', 
        'earned_score', 'notes', 'result'
    ];

    protected $casts = [
        'earned_score' => 'decimal:2',
        'result' => ManifestResult::class,
    ];

    public function manifest(): BelongsTo
    {
        return $this->belongsTo(OperationalManifest::class, 'manifest_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InspectionItem::class, 'inspection_item_id');
    }
}
