<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Intervention\Exception\AntennaAlreadyHasActiveIntervention;
use App\Domain\Intervention\InterventionPriority;
use App\Domain\Intervention\InterventionRepository;
use Illuminate\Database\UniqueConstraintViolationException;

final class EloquentInterventionRepository implements InterventionRepository
{
    private const UNIQUE_ACTIVE_INTERVENTION_PER_ANTENNA = 'uq_one_active_intervention_per_antenna';

    public function createIntervention(int $antennaId, string $description, string $technicianIdentity, InterventionPriority $priority): int
    {
        try {
            $intervention = InterventionModel::create([
                'antenna_id' => $antennaId,
                'description' => $description,
                'technician_identity' => $technicianIdentity,
                'priority' => $priority->value,
            ]);
        } catch (UniqueConstraintViolationException $uniqueConstraintViolationException) {
            if (str_contains($uniqueConstraintViolationException->getMessage(), self::UNIQUE_ACTIVE_INTERVENTION_PER_ANTENNA)) {
                throw new AntennaAlreadyHasActiveIntervention;
            }
            throw $uniqueConstraintViolationException;
        }

        return $intervention->id;
    }

    public function closeIntervention(int $interventionId, string $technicianIdentity): void
    {
    }
}
