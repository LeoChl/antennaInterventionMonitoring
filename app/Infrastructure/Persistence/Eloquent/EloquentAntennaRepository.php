<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Antenna\Antenna;
use App\Domain\Antenna\AntennaRepository;
use App\Domain\Antenna\AntennaStatus;
use App\Domain\Antenna\Exception\AntennaNotFound;
use App\Domain\Intervention\Intervention;
use App\Domain\Intervention\InterventionPriority;
use App\Domain\Shared\PaginatedResult;

final class EloquentAntennaRepository implements AntennaRepository
{
    private function toIntervention(InterventionModel $model): Intervention
    {
        return new Intervention(
            $model->id,
            $model->antenna_id,
            $model->description,
            $model->technician_identity,
            InterventionPriority::from($model->priority),
            \DateTimeImmutable::createFromInterface($model->created_at),
            $model->ended_at ? \DateTimeImmutable::createFromInterface($model->ended_at) : null,
        );
    }

    public function findPaginated(int $page, int $perPage, ?string $city = null, ?AntennaStatus $status = null): PaginatedResult
    {
        $whereCondition = [];

        if ($status) {
            $whereCondition['status'] = $status->value;
        }

        if ($city) {
            $whereCondition['city'] = $city;
        }

        $rawAntennas = AntennaModel::with('latestIntervention')
            ->where($whereCondition)
            ->orderBy('id')
            ->paginate($perPage, ['*'], 'page', $page);

        $consolidatedAntennas = $rawAntennas->getCollection()
            ->map(fn (AntennaModel $antenna) => Antenna::fromArray(
                $antenna->toArray(),
                $antenna->latestIntervention ? $this->toIntervention($antenna->latestIntervention) : null,
            ))
            ->all();

        $paginatedResult = new PaginatedResult($consolidatedAntennas, $rawAntennas->total(), $page, $perPage);

        return $paginatedResult;
    }

    public function findAntennaById(int $antennaId): Antenna
    {
        $antenna = AntennaModel::with('activeIntervention')->find($antennaId);

        if ($antenna === null) {
            throw new AntennaNotFound;
        }

        $intervention = $antenna->activeIntervention ? $this->toIntervention($antenna->activeIntervention) : null;

        return Antenna::fromArray($antenna->toArray(), $intervention);
    }

    public function updateStatus(int $antennaId, AntennaStatus $status): void
    {
        AntennaModel::whereKey($antennaId)->update(['status' => $status->value]);
    }
}
