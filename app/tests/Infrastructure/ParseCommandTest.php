<?php

use App\Domain\Airline;
use App\Domain\Airplane;
use App\Domain\Flight;
use App\Domain\FlightFactory;
use App\Infrastructure\ParseCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ParseCommandTest extends TestCase
{
    private ParseCommand $command;
    private FlightFactory|\PHPUnit\Framework\MockObject\MockObject $flightFactoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->flightFactoryMock = $this->createMock(FlightFactory::class);

        $this->command = new ParseCommand($this->flightFactoryMock);
    }

    public function testExecute()
    {
        $result = $this->command->run(new ArrayInput([]), new BufferedOutput());

        $this->assertEquals(ParseCommand::SUCCESS, $result);
    }

    public function testTopThreeLongestFlights()
    {
        $this->flightFactoryMock
            ->method('createFlightFromJson')
            ->willReturnOnConsecutiveCalls(
                $this->createFlightWithActualDuration(120),
                $this->createFlightWithActualDuration(180),
                $this->createFlightWithActualDuration(90)
            );

        $parseCommandMock = $this->getMockBuilder(ParseCommand::class)
            ->setConstructorArgs([$this->flightFactoryMock])
            ->onlyMethods(['readJsonArrayFromFile'])
            ->getMock();

        $parseCommandMock->method('readJsonArrayFromFile')->willReturn([
            $this->createFlightWithActualDuration(120)->toJsonString(),
            $this->createFlightWithActualDuration(180)->toJsonString(),
            $this->createFlightWithActualDuration(90)->toJsonString(),
        ]);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $result = $parseCommandMock->run($input, $output);

        $this->assertEquals(ParseCommand::SUCCESS, $result);

        $outputContent = $output->fetch();
        $this->assertStringContainsString('Top Three Longest Flights:', $outputContent);
        $this->assertStringContainsString('Test Registration Duration: 120', $outputContent);
        $this->assertStringContainsString('Test Registration Duration: 180', $outputContent);
        $this->assertStringContainsString('Test Registration Duration: 90', $outputContent);
    }

    public function testAirlineWithMostMissedLandings()
    {
        $this->flightFactoryMock
            ->method('createFlightFromJson')
            ->willReturnOnConsecutiveCalls(
                $this->createFlightWithMissedLanding(),
                $this->createFlightWithMissedLanding(),
                $this->createFlightWithoutMissedLanding()
            );

        $parseCommandMock = $this->getMockBuilder(ParseCommand::class)
            ->setConstructorArgs([$this->flightFactoryMock])
            ->onlyMethods(['readJsonArrayFromFile'])
            ->getMock();

        $parseCommandMock->method('readJsonArrayFromFile')->willReturn([
            $this->createFlightWithMissedLanding()->toJsonString(),
            $this->createFlightWithMissedLanding()->toJsonString(),
            $this->createFlightWithoutMissedLanding()->toJsonString(),
        ]);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $result = $parseCommandMock->run($input, $output);

        $this->assertEquals(ParseCommand::SUCCESS, $result);

        $outputContent = $output->fetch();
        $this->assertStringContainsString('Airline with Most Missed Landings:', $outputContent);
        $this->assertStringContainsString('Test Airline', $outputContent);
    }

    public function testDestinationWithMostOvernightStays()
    {
        $this->flightFactoryMock
            ->method('createFlightFromJson')
            ->willReturnOnConsecutiveCalls(
                $this->createFlightWithOvernightStay(),
                $this->createFlightWithOvernightStay(),
                $this->createFlightWithoutOvernightStay()
            );

        $parseCommandMock = $this->getMockBuilder(ParseCommand::class)
            ->setConstructorArgs([$this->flightFactoryMock])
            ->onlyMethods(['readJsonArrayFromFile'])
            ->getMock();

        $parseCommandMock->method('readJsonArrayFromFile')->willReturn([
            $this->createFlightWithOvernightStay()->toJsonString(),
            $this->createFlightWithOvernightStay()->toJsonString(),
            $this->createFlightWithoutOvernightStay()->toJsonString(),
        ]);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $result = $parseCommandMock->run($input, $output);

        $this->assertEquals(ParseCommand::SUCCESS, $result);

        $outputContent = $output->fetch();
        $this->assertStringContainsString('Destination with Most Overnight Stays:', $outputContent);
        $this->assertStringContainsString('Test Airport To', $outputContent);
    }

    private function createFlightWithMissedLanding(): Flight
    {
        $airline = new Airline('Test Airline');
        $airplane = new Airplane('Test Registration', $airline);
        $scheduledStart = new \DateTimeImmutable("2023-01-01T10:00:00+00:00");
        $scheduledEnd = $scheduledStart->modify("+60 minutes");
        $actualStart = $scheduledStart->modify("+12 minutes");
        $actualEnd = $actualStart->modify("+50 minutes");

        return new Flight($airplane, "Test Airport From", "Test Airport To", $scheduledStart, $scheduledEnd, $actualStart, $actualEnd);
    }

    private function createFlightWithActualDuration(int $duration): Flight
    {
        $airline = new Airline('Test Airline');
        $airplane = new Airplane('Test Registration', $airline);
        $from = "Test Airport From";
        $to = "Test Airport To";
        $scheduledStart = new \DateTimeImmutable("2023-01-01T10:00:00+00:00");
        $scheduledEnd = $scheduledStart->modify("+{$duration} minutes");
        $actualStart = $scheduledStart->modify("+12 minutes");
        $actualEnd = $actualStart->modify("+{$duration} minutes");

        return new Flight($airplane, $from, $to, $scheduledStart, $scheduledEnd, $actualStart, $actualEnd);
    }

    private function createFlightWithoutMissedLanding(): Flight
    {
        return $this->createFlightWithActualDuration(180);
    }

    private function createFlightWithOvernightStay(): Flight
    {
        $airline = new Airline('Test Airline');
        $airplane = new Airplane('Test Registration', $airline);
        $scheduledStart = new \DateTimeImmutable("2023-01-01T22:55:00+00:00");
        $scheduledEnd = $scheduledStart->modify("+60 minutes");
        $actualStart = $scheduledStart->modify("+12 minutes");
        $actualEnd = $actualStart->modify("+55 minutes");

        return new Flight($airplane, "Test Airport From", "Test Airport To", $scheduledStart, $scheduledEnd, $actualStart, $actualEnd);
    }

    private function createFlightWithoutOvernightStay(): Flight
    {
        return $this->createFlightWithActualDuration(180);
    }
}
