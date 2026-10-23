<?php

declare(strict_types=1);

namespace App\Domain\Intervention;

final class Intervention
{
    public function __construct(
        public readonly int $id,
        public readonly int $antennaId,
        public readonly string $description,
        public readonly string $technicianIdentity,
        public readonly InterventionPriority $priority,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $endedAt = null,
    ) {}

    public function isInterventionOngoing(): bool
    {
        return $this->endedAt === null;
    }
}
