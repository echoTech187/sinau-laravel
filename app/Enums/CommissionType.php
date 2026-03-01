<?php

namespace App\Enums;

enum CommissionType: string
{
    case PERCENTAGE = 'percentage';
    case FLAT = 'flat';
}
