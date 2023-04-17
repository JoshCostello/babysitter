<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EarningsCalculationTest extends TestCase
{
    /** @test */
    public function it_can_see_the_calculation_form(): void
    {
        $this->get('/')->assertStatus(200)->assertSee('Babysitter Kata');
    }

    /** @test */
    public function it_can_calculate_earnings(): void
    {
        $this->post('/', ['arrival' => '1/1/2023 6:00pm', 'departure' => '1/2/2023 12:00am'])
            ->assertStatus(200)
            ->assertSee('72.00')
            ->assertSessionHasNoErrors();
    }

    /** @test */
    public function it_validates_calculation_requests(): void
    {
        $this->post('/', ['arrival' => 'This is not a date', 'departure' => '1/2/2023 12:00am'])
            ->assertSessionHasErrors(['arrival']);

        $this->followingRedirects()
            ->post('/', ['arrival' => '1/1/2023 6:00pm', 'departure' => '1/12/2023 12:00am'])
            ->assertSee("Invalid number of hours in this shift");

        $this->post('/', [
            'arrival' => '1/1/2023 6:00pm',
            'bedtime' => '1/1/2023 2:00pm',
            'departure' => '1/2/2023 12:00am'
            ])->assertSessionHasErrors(['bedtime']);
    }
}
