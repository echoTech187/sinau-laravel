<?php

namespace App\Enums;

enum MaintenanceType: string
{
    case REACTIVE = 'reactive';
    case PREVENTIVE = 'preventive';
}
