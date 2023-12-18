<?php

namespace App\Test\Domain;

use App\Domain\Airline;
use App\Domain\Airplane;
use App\Domain\Flight;
use PHPUnit\Framework\TestCase;

class FlightTest extends TestCase
{
    public function testGetActualDuration()
    {
        $airline = new Airline('Test Airline');
        $airplane = new Airplane('Test Registration', $airline);
        $flight = new Flight(
            $airplane,
            'FromAirport',
            'ToAirport',
            new \DateTimeImmutable("2023-01-01T10:00:00+00:00"),
            new \DateTimeImmutable("2023-01-01T12:00:00+00:00"),
            new \DateTimeImmutable("2023-01-01T10:30:00+00:00"),
            new \DateTimeImmutable("2023-01-01T12:30:00+00:00")
        );

        $this->assertEquals(120, $flight->getActualDuration());
    }

    public function testIsLandingMissed()
    {
        // Scenario 1: Landing missed (more than 5 minutes delay)
        $airline = new Airline('Test Airline');
        $airplane = new Airplane('Test Registration', $airline);
        $flightMissed = new Flight(
            $airplane,
            'FromAirport',
            'ToAirport',
            new \DateTimeImmutable('2023-01-01T10:00:00+00:00'),
            new \DateTimeImmutable('2023-01-01T12:00:00+00:00'),
            new \DateTimeImmutable('2023-01-01T10:30:00+00:00'),
            new \DateTimeImmutable('2023-01-01T12:10:00+00:00')  // More than 5 minutes delay
        );
        $this->assertTrue($flightMissed->isLandingMissed());

        // Scenario 2: Landing not missed (within 5 minutes delay)
        $flightNotMissed = new Flight(
            $airplane,
            'FromAirport',
            'ToAirport',
            new \DateTimeImmutable('2023-01-01T10:00:00+00:00'),
            new \DateTimeImmutable('2023-01-01T12:00:00+00:00'),
            new \DateTimeImmutable('2023-01-01T10:30:00+00:00'),
            new \DateTimeImmutable('2023-01-01T11:55:00+00:00')  // Within 5 minutes delay
        );
        $this->assertFalse($flightNotMissed->isLandingMissed());
    }

    public function testHasOvernightStay()
    {
        // Scenario 1: Flight with overnight stay
        $airline = new Airline('Test Airline');
        $airplane = new Airplane('Test Registration', $airline);

        $flightOvernight = new Flight(
            $airplane,
            'FromAirport',
            'ToAirport',
            new \DateTimeImmutable('2023-01-01T22:30:00+00:00'),
            new \DateTimeImmutable('2023-01-01T23:50:00+00:00'),
            new \DateTimeImmutable('2023-01-01T23:10:00+00:00'),
            new \DateTimeImmutable('2023-01-02T01:50:00+00:00')
        );
        $this->assertTrue($flightOvernight->hasOvernightStay(), 'Flight with overnight stay should return true.');

        // Scenario 2: Flight without overnight stay
        $flightNoOvernight = new Flight(
            $airplane,
            'FromAirport',
            'ToAirport',
            new \DateTimeImmutable('2023-01-01T10:00:00+00:00'),
            new \DateTimeImmutable('2023-01-01T12:00:00+00:00'),
            new \DateTimeImmutable('2023-01-01T10:30:00+00:00'),
            new \DateTimeImmutable('2023-01-01T11:55:00+00:00')
        );
        $this->assertFalse($flightNoOvernight->hasOvernightStay(), 'Flight without overnight stay should return false.');
    }
}
