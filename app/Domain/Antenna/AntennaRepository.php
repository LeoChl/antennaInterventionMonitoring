<?php

declare(strict_types=1);

namespace App\Domain\Antenna;

use App\Domain\Shared\PaginatedResult;

interface AntennaRepository
{
    public function searchAntennas(int $page, int $perPage, ?string $city = null, ?AntennaStatus $status = null): PaginatedResult;

    public function findAntennaById(int $antennaId): Antenna;

    public function updateStatus(int $antennaId, AntennaStatus $status): void;
}
