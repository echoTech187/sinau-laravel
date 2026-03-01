<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessRequest extends Model
{
    use HasUuids;

    protected $fillable = [
        'requester_id',
        'target_user_id',
        'type',
        'requested_changes',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_note',
    ];

    protected $casts = [
        'requested_changes' => 'array',
        'reviewed_at'       => 'datetime',
    ];

    // ─── Relationships ─────────────────────────────────────────

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ─── Scopes ────────────────────────────────────────────────

    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', 'pending');
    }

    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('status', 'approved');
    }

    public function scopeRejected(Builder $q): Builder
    {
        return $q->where('status', 'rejected');
    }

    // ─── Helpers ───────────────────────────────────────────────

    public function typeLabel(): string
    {
        return match ($this->type) {
            'role_grant'        => 'Tambah Role',
            'role_revoke'       => 'Cabut Role',
            'permission_grant'  => 'Tambah Permission',
            'permission_revoke' => 'Cabut Permission',
            default             => $this->type,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending'  => 'text-amber-600 bg-amber-50 dark:bg-amber-900/30 dark:text-amber-300 border-amber-200 dark:border-amber-700/50',
            'approved' => 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700/50',
            'rejected' => 'text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-300 border-red-200 dark:border-red-700/50',
            default    => 'text-zinc-600 bg-zinc-100',
        };
    }

    public function typeColor(): string
    {
        return match ($this->type) {
            'role_grant', 'permission_grant'   => 'text-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 dark:text-indigo-300',
            'role_revoke', 'permission_revoke' => 'text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-300',
            default => 'text-zinc-600 bg-zinc-100',
        };
    }
}
