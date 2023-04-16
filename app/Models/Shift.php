<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Exception;

class Shift
{
    const EARLIEST_ARRIVAL = 17; // 5:00pm
    const LATEST_DEPARTURE = 4; // 4:00am
    public CarbonImmutable $arrivalTime;
    public ?CarbonImmutable $bedtime;
    public CarbonImmutable $departureTime;

    public function __construct(
        CarbonImmutable $arrivalTime,
        CarbonImmutable $departureTime,
        ?CarbonImmutable $bedtime = null,
    ){
        $this->isValid($arrivalTime, $departureTime, $bedtime);
    }

    public function isValid(CarbonImmutable $arrival, CarbonImmutable $departure, ?CarbonImmutable $bedtime = null): void
    {
        if ($arrival->startOfHour()->hour < self::EARLIEST_ARRIVAL && $arrival->startOfHour()->hour >= self::LATEST_DEPARTURE) {
            throw new Exception("Shifts start between 5pm and 3am");
        }

        if ($departure->startOfHour()->addHour()->hour > self::LATEST_DEPARTURE && $departure->startOfHour()->addHour()->hour <= self::EARLIEST_ARRIVAL) {
            throw new Exception("Shifts may only end between 6pm and 4am");
        }

        if (!$arrival->isSameDay($departure)
            && $departure->startOfHour()->addHour()->greaterThan($arrival->startOfDay()->addDay()->addHours(4))
        ) {
            throw new Exception("A shift may only be for one night starting at 5pm and finishing by 4am");
        }

        if ($bedtime) {
            if ($arrival->startOfHour()->greaterThan($bedtime->startOfHour()->addHour())
                || $departure->startOfHour()->addHour()->lessThanOrEqualTo($bedtime->startOfHour()->addHour())
            ) {
                throw new Exception("Bedtime must be at or after arrival and before departure.");
            }
        }
    }
}
