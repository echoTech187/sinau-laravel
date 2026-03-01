<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menus extends Model
{
    protected $table = 'menus';

    protected $fillable = [
        'id',
        'module_id',
        'parent_id',
        'permission_id',
        'name',
        'icon',
        'route',
        'order',
        'is_active',
        'target',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function module()
    {
        return $this->belongsTo(Modules::class, 'module_id');
    }

    public function parent()
    {
        return $this->belongsTo(Menus::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menus::class, 'parent_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permissions::class, 'permission_id');
    }
}
