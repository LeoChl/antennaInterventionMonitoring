<?php

declare(strict_types=1);

namespace App\Application\Antenna\ListAntennas;

use App\Domain\Antenna\AntennaStatus;

final class ListAntennasQuery
{
    public function __construct(
        public readonly int $page,
        public readonly int $perPage,
        public readonly ?string $city = null,
        public readonly ?AntennaStatus $status = null,
    ) {}
}
