<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modules extends Model
{
    //
    protected $table = 'modules';

    protected $fillable = [
        'id',
        'icon',
        'name',
        'label',
        'order',
        'is_active',
    ];

    public function children()
    {
        return $this->hasMany(Modules::class, 'parent_id');
    }

    public function permissions()
    {
        return $this->hasMany(Permissions::class, 'module_id');
    }
}
