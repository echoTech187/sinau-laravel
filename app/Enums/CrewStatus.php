<?php

namespace App\Enums;

enum CrewStatus: string
{
    case ACTIVE = 'active';
    case ON_LEAVE = 'on_leave';
    case SUSPENDED = 'suspended';
    case INACTIVE = 'inactive';
}
