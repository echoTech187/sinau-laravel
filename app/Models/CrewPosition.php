<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrewPosition extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'base_allowance'];

    protected $casts = [
        'base_allowance' => 'decimal:2',
    ];

    public function crews(): HasMany
    {
        return $this->hasMany(Crew::class);
    }
}
