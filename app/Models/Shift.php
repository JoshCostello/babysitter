<?php

namespace App\Models;

use App\Enums\Rates;
use Carbon\CarbonImmutable;
use Exception;

class Shift
{
    const EARLIEST_ARRIVAL = 17; // 5:00pm
    const LATEST_DEPARTURE = 4; // 4:00am
    const MAX_LENGTH = 11;
    const OVERTIME_PREMIUM = 0.5;
    const STANDARD_LENGTH = 8; // Quantity of hours worked before being eligible for overtime

    public CarbonImmutable $arrivalTime;
    public CarbonImmutable $departureTime;
    public ?CarbonImmutable $bedtime;

    public function __construct(
        CarbonImmutable $arrivalTime,
        CarbonImmutable $departureTime,
        ?CarbonImmutable $bedtime = null,
    ){
        $this->arrivalTime = $arrivalTime->startOfHour();
        $this->departureTime = $departureTime->minute === 0 ? $departureTime : $departureTime->startOfHour()->addHour();
        $this->bedtime = is_null($bedtime) ? null : ($bedtime->minute === 0 ? $bedtime : $bedtime->startOfHour()->addHour());

        $this->validate();
    }

    /** Bedtime hours before midnight */
    public function bedtimeHours(): int
    {
        if ($this->bedtime === null || $this->bedtime->hour < self::LATEST_DEPARTURE) {
            return 0;
        }

        if ($this->departureTime->hour <= self::LATEST_DEPARTURE) {
            return 24 - $this->bedtime->hour;
        }

        return $this->departureTime->hour - $this->bedtime->hour;
    }

    public function calculateEarnings(): string
    {
        $earnings = $this->earnsOvertime()
            ? $this->standardEarnings() + $this->overtimeEarnings()
            : $this->standardEarnings();

        return number_format($earnings, 2);
    }

    public function earnsOvertime(): bool
    {
        return $this->departureTime->diffInHours($this->arrivalTime) > self::STANDARD_LENGTH;
    }

    public function overtimeHoursWorked(): int
    {
        return $this->earnsOvertime()
            ? $this->departureTime->diffInHours($this->arrivalTime) - self::STANDARD_LENGTH
            : 0;
    }

    public function postMidnightHours(): int
    {
        if ($this->departureTime->hour > self::LATEST_DEPARTURE) {
            return 0;
        }

        return $this->arrivalTime->hour > self::LATEST_DEPARTURE
            ? $this->departureTime->hour
            : $this->departureTime->hour - $this->arrivalTime->hour;
    }

    public function standardHours(): int
    {
        if ($this->arrivalTime->hour < self::LATEST_DEPARTURE) {
            return 0;
        }

        if ($this->bedtimeHours() > 0) {
            return $this->bedtime->hour - $this->arrivalTime->hour;
        }

        if ($this->departureTime->hour <= self::LATEST_DEPARTURE) {
            return 24 - $this->arrivalTime->hour;
        }

        return $this->departureTime->hour - $this->arrivalTime->hour;
    }

    protected function blendedRate(): float
    {
        return ($this->standardEarnings() / ($this->departureTime->diffInHours($this->arrivalTime)));
    }

    protected function overtimeEarnings(): float
    {
        return $this->overtimeHoursWorked() * $this->overtimeRate();
    }

    protected function overtimeRate(): float
    {
        return $this->blendedRate() * self::OVERTIME_PREMIUM;
    }

    protected function standardEarnings(): float
    {
        $amount = ($this->standardHours() * Rates::ARRIVAL_TO_BEDTIME->value)
            + ($this->bedtimeHours() * Rates::BEDTIME_TO_MIDNIGHT->value)
            + ($this->postMidnightHours() * Rates::MIDNIGHT_TO_DEPARTURE->value);

        return $amount / 100;
    }

    protected function validate(): void
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
