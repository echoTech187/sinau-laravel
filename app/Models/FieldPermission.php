<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldPermission extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'field_permissions';

    /**
     * Indicates if the model should be stamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'model',
        'field',
        'is_hidden',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    /**
     * Get the role that owns the field permission.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }
}
