<?php

namespace App\Infrastructure;

class AirlineLookup
{
    /**
     * @var array<string, string>
     */
    private static array $map = [
        'HA-AAA' => 'Alpha Airlines',
        'HA-AAB' => 'Alpha Airlines',
        'HA-AAC' => 'Alpha Airline',

        'D-AAA' => 'Delta Freight',
        'D-AAB' => 'Delta Freight',
        'D-AAC' => 'Delta Freight',

        'OO-AAA' => 'Oscar Air',
        'OO-AAB' => 'Oscar Air',
        'OO-AAC' => 'Oscar Air',
    ];

    public static function from(string $registration): string
    {
        if (array_key_exists($registration, self::$map)) {
            return self::$map[$registration];
        }

        throw new \RuntimeException('Missing registration mapping.');
    }
}