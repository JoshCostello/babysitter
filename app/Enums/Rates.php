<?php

namespace App\Enums;

/**
 * Hourly rates are in cents.
 */
enum Rates: int
{
    case ARRIVAL_TO_BEDTIME = 1200;
    case BEDTIME_TO_MIDNIGHT = 800;
    case MIDNIGHT_TO_DEPARTURE = 1600;
}
