<?php

namespace App\Enums;

enum AgentType: string
{
    case BRANCH_OFFICE = 'branch_office';
    case PARTNER_EXCLUSIVE = 'partner_exclusive';
    case PARTNER_GENERAL = 'partner_general';
}
