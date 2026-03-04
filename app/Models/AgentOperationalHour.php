<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentOperationalHour extends Model
{
    protected $fillable = [
        'agent_id',
        'day',
        'open_time',
        'close_time',
        'is_24_hours',
        'notes',
    ];

    protected $casts = [
        'day' => 'integer',
        'is_24_hours' => 'boolean',
    ];

    const DAYS = [
        0 => 'Senin',
        1 => 'Selasa',
        2 => 'Rabu',
        3 => 'Kamis',
        4 => 'Jumat',
        5 => 'Sabtu',
        6 => 'Minggu',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function getDayNameAttribute(): string
    {
        return self::DAYS[$this->day] ?? 'Unknown';
    }

    public function getFormattedHoursAttribute(): string
    {
        if ($this->is_24_hours) return '24 Jam';
        if (!$this->open_time || !$this->close_time) return 'Tutup';
        return substr($this->open_time, 0, 5) . ' - ' . substr($this->close_time, 0, 5);
    }
}
