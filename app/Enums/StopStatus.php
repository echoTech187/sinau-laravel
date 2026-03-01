<?php

namespace App\Enums;

enum StopStatus: string
{
    case PENDING = 'pending';
    case ARRIVED = 'arrived';
    case SKIPPED = 'skipped';
}
