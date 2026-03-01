<?php

namespace App\Enums;

enum AgentStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BANNED = 'banned';
}
