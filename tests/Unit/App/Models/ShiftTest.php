<?php

namespace Tests\Unit\App\Models;

use App\Models\Shift;
use Carbon\CarbonImmutable;
use Exception;
use PHPUnit\Framework\TestCase;

class ShiftTest extends TestCase
{
    /** @test */
    public function it_validates(): void
    {
        // Invalid arrival
        try {
            new Shift(...[
                'arrivalTime' => CarbonImmutable::parse('2023-01-01 12:00:00'),
                'departureTime' => CarbonImmutable::parse('2023-01-02 00:00:00'),
            ]);

            $this->fail();
        } catch (Exception $exception) {
            $this->assertEquals("Shifts start between 5pm and 3am", $exception->getMessage());
        }

        // Invalid departure
        try {
            new Shift(...[
                'arrivalTime' => CarbonImmutable::parse('2023-01-01 17:00:00'),
                'departureTime' => CarbonImmutable::parse('2023-01-02 05:00:00'),
            ]);

            $this->fail();
        } catch (Exception $exception) {
            $this->assertEquals("Shifts may only end between 6pm and 4am", $exception->getMessage());
        }

        // Shift too long
        try {
            new Shift(...[
                'arrivalTime' => CarbonImmutable::parse('2023-01-01 17:00:00'),
                'departureTime' => CarbonImmutable::parse('2023-01-03 20:00:00'),
            ]);

            $this->fail();
        } catch (Exception $exception) {
            $this->assertEquals(
                "A shift may only be for one night starting at 5pm and finishing by 4am",
                $exception->getMessage()
            );
        }

        // Invalid bedtime
        try {
            new Shift(...[
                'arrivalTime' => CarbonImmutable::parse('2023-01-01 17:00:00'),
                'bedtime' => CarbonImmutable::parse('2023-01-01 16:00:00'),
                'departureTime' => CarbonImmutable::parse('2023-01-01 20:00:00'),
            ]);

            $this->fail();
        } catch (Exception $exception) {
            $this->assertEquals(
                "Bedtime must be at or after arrival and before departure.",
                $exception->getMessage()
            );
        }
    }
}
