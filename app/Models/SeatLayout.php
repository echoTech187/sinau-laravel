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
}
