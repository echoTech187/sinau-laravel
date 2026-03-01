<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RbacLog extends Model
{
    protected $table = 'rbac_logs';

    protected $fillable = [
        'actor_id',
        'target_user_id',
        'event',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
