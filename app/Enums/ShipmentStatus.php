<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case RECEIVED_AT_AGENT = 'received_at_agent';
    case LOADED_TO_BUS = 'loaded_to_bus';
    case IN_TRANSIT = 'in_transit';
    case INSPECTED_BY_CHECKER = 'inspected_by_checker';
    case UNLOADED = 'unloaded';
    case CLAIMED_BY_RECEIVER = 'claimed_by_receiver';
}
