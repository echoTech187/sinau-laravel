<?php

namespace App\Enums;

enum ScheduleStatus: string
{
    case SCHEDULED = 'scheduled';
    case BOARDING = 'boarding';
    case ON_THE_WAY = 'on_the_way';
    case ARRIVED = 'arrived';
    case CANCELED = 'canceled';
}
