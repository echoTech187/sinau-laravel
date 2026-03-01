<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentStaff extends Model
{
    use HasFactory;

    protected $fillable = ['agent_id', 'user_id', 'is_admin'];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
