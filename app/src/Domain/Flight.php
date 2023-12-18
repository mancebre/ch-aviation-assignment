<?php

namespace App\Domain;

class Flight
{
    private const LANDING_THRESHOLD = 5;

    public function __construct(
        private readonly Airplane $airplane,
        private readonly string $from,
        private readonly string $to,
        private readonly \DateTimeImmutable $scheduledStart,
        private readonly \DateTimeImmutable $scheduledEnd,
        private readonly \DateTimeImmutable $actualStart,
        private readonly \DateTimeImmutable $actualEnd,
    ) {
    }

    public function getAirplane(): Airplane
    {
        return $this->airplane;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getScheduledStart(): \DateTimeImmutable
    {
        return $this->scheduledStart;
    }

    public function getScheduledEnd(): \DateTimeImmutable
    {
        return $this->scheduledEnd;
    }

    public function getActualStart(): \DateTimeImmutable
    {
        return $this->actualStart;
    }

    public function getActualEnd(): \DateTimeImmutable
    {
        return $this->actualEnd;
    }

    public function getActualDuration(): int
    {
        $actualStart = $this->getActualStart();
        $actualEnd = $this->getActualEnd();
        $duration = $actualStart->diff($actualEnd);

        // Calculate the total duration in minutes
        $totalMinutes = ($duration->days * 24 * 60) + ($duration->h * 60) + $duration->i;

        return $totalMinutes;
    }

    public function isLandingMissed(): bool
    {
        $scheduledEnd = $this->getScheduledEnd();
        $actualEnd = $this->getActualEnd();

        return $scheduledEnd->diff($actualEnd)->i > self::LANDING_THRESHOLD;
    }

    public function hasOvernightStay(): bool
    {
        $scheduledEnd = $this->getScheduledEnd();
        $actualEnd = $this->getActualEnd();

        $scheduledEndDate = $scheduledEnd->format('Y-m-d');
        $actualEndDate = $actualEnd->format('Y-m-d');

        // Check if the actual end is on the next day after the scheduled end (overnight stay)
        return $actualEndDate > $scheduledEndDate;
    }

    public function toJsonString(): string
    {
        $flightData = [
            'registration' => $this->airplane->getRegistration(),
            'from' => $this->from,
            'to' => $this->to,
            'scheduled_start' => $this->scheduledStart->format('Y-m-d\TH:i:sP'),
            'scheduled_end' => $this->scheduledEnd->format('Y-m-d\TH:i:sP'),
            'actual_start' => $this->actualStart->format('Y-m-d\TH:i:sP'),
            'actual_end' => $this->actualEnd->format('Y-m-d\TH:i:sP'),
        ];

        return json_encode($flightData);
    }
}