<?php

namespace App\Enums;

enum BusStatus: string
{
    case ACTIVE = 'active';
    case MAINTENANCE = 'maintenance';
    case INACTIVE = 'inactive';
    case SOLD = 'sold';
}
