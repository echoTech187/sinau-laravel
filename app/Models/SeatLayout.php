<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeatLayout extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'grid_rows', 'grid_columns', 'layout_mapping'];

    protected $casts = [
        'layout_mapping' => 'array',
    ];

    public function buses(): HasMany
    {
        return $this->hasMany(Bus::class);
    }

    /**
     * Get all seats across all decks (flattend) 
     * Handles both legacy (flat array) and new (decks object) structures.
     */
    public function getAllSeatsAttribute(): array
    {
        $mapping = $this->layout_mapping;

        if (empty($mapping)) {
            return [];
        }

        // Check if it's the new multi-deck structure
        if (isset($mapping['decks']) && is_array($mapping['decks'])) {
            return collect($mapping['decks'])->flatMap(function ($deck) {
                return $deck['mapping'] ?? [];
            })->toArray();
        }

        // Legacy: mapping is already a flat array of seats
        return is_array($mapping) ? $mapping : [];
    }
}
