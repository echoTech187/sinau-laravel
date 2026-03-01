<?php

namespace App\Enums;

enum TransactionType: string
{
    case TOPUP = 'topup';
    case TICKET_PURCHASE = 'ticket_purchase';
    case TICKET_REFUND = 'ticket_refund';
    case COMMISSION_PAYOUT = 'commission_payout';
    case ADJUSTMENT = 'adjustment';
}
