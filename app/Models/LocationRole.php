<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LocationRole extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'location_has_role');
    }
}
