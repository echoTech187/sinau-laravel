<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'target_role_id', 'min_passing_percentage'];

    public function items(): HasMany
    {
        return $this->hasMany(InspectionItem::class, 'category_id');
    }
}
