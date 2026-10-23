<?php

declare(strict_types=1);

namespace App\Domain\Intervention;

interface InterventionRepository
{
    public function createIntervention(int $antennaId, string $description, string $technicianIdentity, InterventionPriority $priority): int;

    public function closeIntervention(int $interventionId, string $technicianIdentity): void;
}
