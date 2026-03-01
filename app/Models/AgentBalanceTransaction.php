<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\TransactionType;

class AgentBalanceTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id', 'user_id', 'amount', 'type', 'reference_id'
    ];

    protected $casts = [
        'type' => TransactionType::class,
        'amount' => 'decimal:2',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
