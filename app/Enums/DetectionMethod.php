<?php

namespace App\Enums;

enum DetectionMethod: string
{
    case MANUAL_SCAN = 'manual_scan';
    case AUTOMATIC_GEOFENCE = 'automatic_geofence';
    case PASSENGER_ACTIVITY = 'passenger_activity';
    case LOGICAL_INTERPOLATION = 'logical_interpolation';
}
