<?php

namespace App\Domain;

use App\Infrastructure\AirlineLookup;

class FlightFactory
{
    private $fileReader;

    public function __construct(callable $fileReader = null)
    {
        $this->fileReader = $fileReader;
    }

    public function createFlightFromJson(string $json): Flight
    {
        $data = json_decode($json, true);

        $airlineName = AirlineLookup::from($data['registration']);
        $airline = new Airline($airlineName);

        $airplane = new Airplane($data['registration'], $airline);

        return new Flight(
            $airplane,
            $data['from'],
            $data['to'],
            new \DateTimeImmutable($data['scheduled_start']),
            new \DateTimeImmutable($data['scheduled_end']),
            new \DateTimeImmutable($data['actual_start']),
            new \DateTimeImmutable($data['actual_end'])
        );
    }
}
