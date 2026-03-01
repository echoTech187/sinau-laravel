<?php

namespace App\Enums;

enum OperationalManifestStatus: string
{
    case DRAFT = 'draft';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
