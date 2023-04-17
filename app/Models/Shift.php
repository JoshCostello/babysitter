<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Exception;

class Shift
{
    const EARLIEST_ARRIVAL = 17; // 5:00pm
    const LATEST_DEPARTURE = 4; // 4:00am
    const MAX_LENGTH = 11;
    const STANDARD_LENGTH = 8; // Quantity of hours worked before being eligible for overtime

    public function __construct(
        public CarbonImmutable $arrivalTime,
        public CarbonImmutable $departureTime,
        public ?CarbonImmutable $bedtime = null,
    ){
        $this->validate();
    }

    public function earnsOvertime(): bool
    {
        return $this->departureTime->diffInHours($this->arrivalTime) > self::STANDARD_LENGTH;
    }
    {
    public function validate(): void
    {
        if ($this->arrivalTime->hour < self::EARLIEST_ARRIVAL && $this->arrivalTime->hour >= self::LATEST_DEPARTURE) {
            throw new Exception('Invalid arrival time');
        }

        if ($this->departureTime->hour > self::LATEST_DEPARTURE && $this->departureTime->hour <= self::EARLIEST_ARRIVAL) {
            throw new Exception('Invalid departure time');
        }

        if ($this->arrivalTime->greaterThanOrEqualTo($this->departureTime)) {
            throw new Exception('Arrival must be before departure');
        }

        if ($this->departureTime->greaterThan($this->arrivalTime->addHours(self::MAX_LENGTH))) {
            throw new Exception('Invalid number of hours in this shift');
        }

        if ($this->bedtime) {
            if (!$this->bedtime->between($this->arrivalTime, $this->departureTime)) {
                throw new Exception('Invalid bedtime');
            }
        }
    }
}
