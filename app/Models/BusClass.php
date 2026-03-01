<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusClass extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'free_baggage_kg', 'description'];

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class);
    }

    public function buses(): HasMany
    {
        return $this->hasMany(Bus::class);
    }
}
