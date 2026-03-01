<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentBalance extends Model
{
    use HasFactory;

    protected $fillable = ['agent_id', 'current_balance', 'credit_limit'];

    protected $casts = [
        'current_balance' => 'decimal:2',
        'credit_limit' => 'decimal:2',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
