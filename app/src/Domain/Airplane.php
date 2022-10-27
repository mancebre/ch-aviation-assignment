<?php

namespace App\Domain;

class Airplane
{
    public function __construct(private readonly string $registration, private readonly Airline $airline)
    {
    }

    public function getRegistration(): string
    {
        return $this->registration;
    }

    public function getAirline(): Airline
    {
        return $this->airline;
    }
}