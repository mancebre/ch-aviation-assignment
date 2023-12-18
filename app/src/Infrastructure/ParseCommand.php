<?php

namespace App\Infrastructure;

use App\Domain\FlightFactory;
use App\Domain\Flight;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'parse')]
class ParseCommand extends Command
{
    private \SplPriorityQueue $priorityQueue;
    private array $missedLandingsCount;
    private array $overnightStaysCount;
    private FlightFactory $flightFactory;
    private $datasetFilePath;

    public function __construct(FlightFactory $flightFactory)
    {
        parent::__construct();

        $this->priorityQueue = new \SplPriorityQueue();
        $this->missedLandingsCount = [];
        $this->overnightStaysCount = [];
        $this->flightFactory = $flightFactory;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $this->getDatasetFilePath();
        $jsonArray = $this->readJsonArrayFromFile($filePath);
        $this->processFile($this->flightFactory, $jsonArray);

        $output->writeln('<info>Top Three Longest Flights:</info>');
        $this->outputTopThreeFlights($output);

        $output->writeln('<info>Airline with Most Missed Landings:</info>');
        $output->writeln($this->outputAirlineWithMostMissedLandings());
        $output->writeln('<info>Destination with Most Overnight Stays:</info>');
        $output->writeln($this->outputDestinationWithMostOvernightStays());

        return Command::SUCCESS;
    }

    private function processFile(FlightFactory $flightFactory, array $jsonArray): void
    {
        foreach ($jsonArray as $json) {
            $flight = $this->createFlightFromJson($flightFactory, $json);

            $this->identifyThreeLongestFlights($flight);
            $this->identifyAirlineWithMostMissedLandings($flight);
            $this->identifyDestinationWithMostOvernightStays($flight);
        }
    }

    private function validateFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException('Error validating the dataset file: ' . $filePath);
        }
    }

    private function openFile(string $filePath)
    {
        $file = fopen($filePath, 'r');
        if (!$file) {
            throw new \RuntimeException('Error opening dataset file: ' . $filePath);
        }

        return $file;
    }

    private function createFlightFromJson(FlightFactory $flightFactory, string $json): Flight
    {
        return $flightFactory->createFlightFromJson($json);
    }

    private function identifyThreeLongestFlights(Flight $flight): void
    {
        // Task 1: Identify the Three Longest Flights by Actual Duration
        $this->priorityQueue->insert($flight, $flight->getActualDuration());
    }

    private function identifyAirlineWithMostMissedLandings(Flight $flight): void
    {
        // Task 2: Identify the Airline with the Most Missed Landings
        if ($flight->isLandingMissed()) {
            $airlineName = $flight->getAirplane()->getAirline()->getName();
            $this->missedLandingsCount[$airlineName] = ($this->missedLandingsCount[$airlineName] ?? 0) + 1;
        }
    }

    private function identifyDestinationWithMostOvernightStays(Flight $flight): void
    {
        // Task 3: Identify the Destination with the Most Overnight Stays
        if ($flight->hasOvernightStay()) {
            $destination = $flight->getTo();
            $this->overnightStaysCount[$destination] = ($this->overnightStaysCount[$destination] ?? 0) + 1;
        }
    }

    private function outputTopThreeFlights(OutputInterface $output): void
    {
        $count = 0;
        while (!$this->priorityQueue->isEmpty() && $count < 3) {
            $flight = $this->priorityQueue->extract();
            $output->writeln('Flight: ' . $flight->getAirplane()->getRegistration() . ' Duration: ' . $flight->getActualDuration() . ' minutes');
            $count++;
        }
    }

    private function identifyMaxCount(array $countArray, string $errorMessage): ?string
    {
        if (empty($countArray)) {
            return $errorMessage;
        }

        $maxItem = array_search(max($countArray), $countArray);

        return $maxItem;
    }

    private function outputAirlineWithMostMissedLandings(): string
    {
        return $this->identifyMaxCount($this->missedLandingsCount, 'No missed landings found.');
    }

    private function outputDestinationWithMostOvernightStays(): string
    {
        return $this->identifyMaxCount($this->overnightStaysCount, 'No flights with overnight stays found.');
    }

    private function getDatasetFilePath(): string
    {
        return __DIR__ . '/../../var/input.jsonl';
    }

    public function setFlightFactory(FlightFactory $flightFactory): void
    {
        $this->flightFactory = $flightFactory;
    }

    public function setDatasetFilePath(string $filePath): void
    {
        $this->datasetFilePath = $filePath;
    }

    protected function readJsonArrayFromFile(string $filePath): array
    {
        $this->validateFile($filePath);
        $file = $this->openFile($filePath);

        $jsonArray = [];
        while (($json = fgets($file)) !== false) {
            $jsonArray[] = $json;
        }

        fclose($file);

        return $jsonArray;
    }
}
