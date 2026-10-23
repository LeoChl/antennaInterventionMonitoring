<?php

declare(strict_types=1);

namespace App\Domain\Antenna;

use App\Domain\Intervention\Intervention;

final class Antenna
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $city,
        public readonly AntennaStatus $status,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?Intervention $latestIntervention = null,
    ) {}

    public function hasActiveIntervention(): bool
    {
        return $this->latestIntervention?->isInterventionOngoing() ?? false;
    }
}
