<?php

namespace App\Tests\Infrastructure;

use App\Infrastructure\AirlineLookup;
use PHPUnit\Framework\TestCase;

class AirlineLookupTest extends TestCase
{
    public function testLookupThrowsExceptionWhenNoMapping(): void
    {
        $this->expectException(\RuntimeException::class);

        AirlineLookup::from('unknown');
    }

    public function testLookupFromRegistration(): void
    {
        $this->assertSame('Alpha Airlines', AirlineLookup::from('HA-AAC'));
    }
}
