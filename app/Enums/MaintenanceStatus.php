<?php

namespace App\Enums;

enum MaintenanceStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
}
