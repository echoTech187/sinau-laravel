<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'roles';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'role',
        'slug',
        'description',
        'is_active',
        'parent_id',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }


    public function permissions()
    {
        return $this->belongsToMany(Permissions::class, 'role_has_permissions', 'role_id', 'permission_id');
    }

    public function getUserAvatarUrl(User $user): string
    {
        return $user->getAvatarUrlAttribute();
    }

    public function role_has_permissions()
    {
        return $this->hasMany(RolePermissions::class, 'role_id');
    }
}
