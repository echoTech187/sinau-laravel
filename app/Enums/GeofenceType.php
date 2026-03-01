<?php

namespace App\Enums;

enum GeofenceType: string
{
    case CIRCULAR = 'circular';
    case POLYGON = 'polygon';
}
