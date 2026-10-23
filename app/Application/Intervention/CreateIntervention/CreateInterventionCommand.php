<?php

declare(strict_types=1);

namespace App\Application\Intervention\CreateIntervention;

use App\Domain\Intervention\InterventionPriority;

final class CreateInterventionCommand
{
    public function __construct(
        public readonly int $antennaId,
        public readonly string $description,
        public readonly string $technicianIdentity,
        public readonly InterventionPriority $priority,
    ) {}
}
