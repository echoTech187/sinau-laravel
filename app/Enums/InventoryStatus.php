<?php

namespace App\Enums;

enum InventoryStatus: string
{
    case AVAILABLE = 'available';
    case MISSING = 'missing';
    case DAMAGED = 'damaged';
}
