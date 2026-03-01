<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\AgentType;
use App\Enums\CommissionType;
use App\Enums\AgentStatus;

class Agent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agent_code', 'location_id', 'parent_branch_id', 'name',
        'phone_number', 'type', 'commission_type', 'commission_value', 'status'
    ];

    protected $casts = [
        'type' => AgentType::class,
        'commission_type' => CommissionType::class,
        'status' => AgentStatus::class,
        'commission_value' => 'decimal:2',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function parentBranch(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'parent_branch_id');
    }

    public function subBranches(): HasMany
    {
        return $this->hasMany(Agent::class, 'parent_branch_id');
    }

    public function balance(): HasOne
    {
        return $this->hasOne(AgentBalance::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(AgentBalanceTransaction::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(AgentStaff::class);
    }
}
