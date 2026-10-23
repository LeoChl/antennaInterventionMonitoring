<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Antenna\Antenna;
use App\Domain\Antenna\AntennaRepository;
use App\Domain\Antenna\AntennaStatus;
use App\Domain\Antenna\Exception\AntennaNotFound;
use App\Domain\Shared\PaginatedResult;

final class EloquentAntennaRepository implements AntennaRepository
{
    public function searchAntennas(int $page, int $perPage, ?string $city = null, ?AntennaStatus $status = null): PaginatedResult
    {
        $query = AntennaModel::with('latestIntervention');

        if ($status) {
            $query->where('status', $status->value);
        }

        if ($city) {
            $query->whereLike('city', "%{$city}%");
        }

        $rawAntennas = $query
            ->orderBy('id')
            ->paginate($perPage, ['*'], 'page', $page);

        $consolidatedAntennas = $rawAntennas->getCollection()
            ->map(fn (AntennaModel $antenna) => $antenna->toDomain($antenna->latestIntervention?->toDomain()))
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

        return $antenna->toDomain($antenna->activeIntervention?->toDomain());
    }

    public function updateStatus(int $antennaId, AntennaStatus $status): void
    {
        AntennaModel::whereKey($antennaId)->update(['status' => $status->value]);
    }
}
