<?php

declare(strict_types=1);

namespace App\Application\Intervention\CloseIntervention;

final class CloseInterventionCommand
{
    public function __construct(
        public readonly int $interventionId,
        public readonly string $description,
        public readonly string $technicianIdentity,
        public readonly ?\DateTimeImmutable $endDate = null,
    ) {}
}
