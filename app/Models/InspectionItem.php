<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionItem extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'item_name', 'max_score', 'is_critical'];

    protected $casts = [
        'is_critical' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(InspectionCategory::class);
    }
}
