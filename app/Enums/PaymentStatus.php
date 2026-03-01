<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case EXPIRED = 'expired';
    case CANCELED = 'canceled';
}
