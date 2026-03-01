<?php

namespace App\Enums;

enum StopType: string
{
    case BOARDING_ONLY = 'boarding_only';
    case DROPOFF_ONLY = 'dropoff_only';
    case BOTH = 'both';
    case TRANSIT = 'transit';
}
