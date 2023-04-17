<?php

namespace Tests\Unit\App\Models;

use App\Models\Shift;
use Carbon\CarbonImmutable;
use Exception;
use PHPUnit\Framework\TestCase;

class ShiftTest extends TestCase
{
    public CarbonImmutable $validArrival;
    public CarbonImmutable $validDeparture;
    public CarbonImmutable $validBedtime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validArrival = CarbonImmutable::parse('2023-01-01 17:00:00');
        $this->validDeparture = CarbonImmutable::parse('2023-01-02 04:00:00');
        $this->validBedtime = CarbonImmutable::parse('2023-01-01 20:00:00');
    }

    /** @test */
    public function it_can_create_a_shift(): void
    {
        $shift = new Shift(...[
            'arrivalTime' => $this->validArrival,
            'departureTime' => $this->validDeparture,
        ]);

        $this->assertNotNull($shift->arrivalTime);
        $this->assertNotNull($shift->departureTime);
    }

    /** @test */
    public function it_validates_arrival_time(): void
    {
        try {
            new Shift(...[
                'arrivalTime' => CarbonImmutable::parse('2023-01-01 12:00:00'),
                'departureTime' => $this->validDeparture,
            ]);

            $this->fail("Failed to catch out-of-bounds arrival");
        } catch (Exception $exception) {
            $this->assertEquals("Invalid arrival time", $exception->getMessage());
        }

        try {
            new Shift(...[
                'arrivalTime' => CarbonImmutable::parse('2023-01-01 20:00:00'),
                'departureTime' => CarbonImmutable::parse('2023-01-01 19:00:00'),
            ]);

            $this->fail("Failed to catch arrival before departure");
        } catch (Exception $exception) {
            $this->assertEquals("Arrival must be before departure", $exception->getMessage());
        }
    }

    /** @test */
    public function it_validates_departure_time(): void
    {
        try {
            new Shift(...[
                'arrivalTime' => $this->validArrival,
                'departureTime' => CarbonImmutable::parse('2023-01-02 12:00:00'),
            ]);

            $this->fail("Failed to catch out-of-bounds departure");
        } catch (Exception $exception) {
            $this->assertEquals("Invalid departure time", $exception->getMessage());
        }
    }

    /** @test */
    public function it_validates_too_many_hours(): void
    {
        try {
            new Shift(...[
                'arrivalTime' => $this->validArrival,
                'departureTime' => CarbonImmutable::parse('2023-01-03 20:00:00'),
            ]);

            $this->fail("Failed to catch hours overage");
        } catch (Exception $exception) {
            $this->assertEquals("Invalid number of hours in this shift", $exception->getMessage());
        }
    }

    /** @test */
    public function it_validates_valid_bedtime(): void
    {
        try {
            new Shift(...[
                'arrivalTime' => $this->validArrival,
                'departureTime' => $this->validDeparture,
                'bedtime' => CarbonImmutable::parse('2023-01-02 12:00:00'),
            ]);

            $this->fail("Failed to catch an invalid bedtime");
        } catch (Exception $exception) {
            $this->assertEquals("Invalid bedtime", $exception->getMessage());
        }
    }

    /** @test */
    public function it_can_determine_if_overtime_was_worked(): void
    {
        $withOvertime = new Shift(...[
            'arrivalTime' => CarbonImmutable::parse('2023-01-01 17:00:00'),
            'departureTime' => CarbonImmutable::parse('2023-01-02 03:00:00'),
        ]);

        $this->assertTrue($withOvertime->earnsOvertime());

        $withoutOvertime = new Shift(...[
            'arrivalTime' => CarbonImmutable::parse('2023-01-01 17:00:00'),
            'departureTime' => CarbonImmutable::parse('2023-01-01 20:00:00'),
        ]);

        $this->assertNotTrue($withoutOvertime->earnsOvertime());
    }

    /** @test */
    public function it_calculates_overtime_hours_worked(): void
    {
        $withOvertime = new Shift(...[
            'arrivalTime' => CarbonImmutable::parse('2023-01-01 17:00:00'),
            'departureTime' => CarbonImmutable::parse('2023-01-02 04:00:00'),
        ]);

        $this->assertEquals(3, $withOvertime->overtimeHoursWorked());

        $withoutOvertime = new Shift(...[
            'arrivalTime' => CarbonImmutable::parse('2023-01-01 17:00:00'),
            'departureTime' => CarbonImmutable::parse('2023-01-01 20:00:00'),
        ]);

        $this->assertEquals(0, $withoutOvertime->overtimeHoursWorked());
    }

    /** @test */
    public function it_calculates_post_midnight_hours(): void
    {
        $shift = new Shift(...[
            'arrivalTime' => $this->validArrival,
            'departureTime' => $this->validDeparture,
        ]);

        $this->assertEquals(4, $shift->postMidnightHours());

        $shift->arrivalTime = CarbonImmutable::parse('2023-01-02 01:00:00');

        $this->assertEquals(3, $shift->postMidnightHours());

        $shift->arrivalTime = CarbonImmutable::parse('2023-01-01 17:00:00');
        $shift->departureTime = CarbonImmutable::parse('2023-01-01 20:00:00');

        $this->assertEquals(0, $shift->postMidnightHours());
    }

    /** @test */
    public function it_calculates_bedtime_hours(): void
    {
        $shift = new Shift(...[
            'arrivalTime' => $this->validArrival,
            'departureTime' => $this->validDeparture,
            'bedtime' => $this->validBedtime,
        ]);

        $this->assertEquals(4, $shift->bedtimeHours());

        $shift->bedtime = CarbonImmutable::parse('2023-01-02 01:00:00');

        $this->assertEquals(0, $shift->bedtimeHours());
    }
}
