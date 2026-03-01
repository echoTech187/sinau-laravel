<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\InventoryStatus;

class BusInventory extends Model
{
    use HasFactory;

    protected $fillable = ['bus_id', 'item_name', 'serial_number', 'status'];

    protected $casts = [
        'status' => InventoryStatus::class,
    ];

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }
}
