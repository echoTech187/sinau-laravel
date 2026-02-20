<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    //
    protected $table = 'permissions';

    protected $fillable = [
        'id',
        'name',
        'slug',
        'module_id',
        'group_name',
    ];

    public function module()
    {
        return $this->belongsTo(Modules::class, 'module_id');
    }
}
