<?php

declare(strict_types=1);

namespace App\Application\Antenna\ListAntennas;

use App\Domain\Antenna\AntennaRepository;

final class ListAntennasHandler
{
    public function __construct(
        private readonly AntennaRepository $antennaRepository,
    ) {}

    public function __invoke(ListAntennasQuery $query): array
    {
        return $this->antennaRepository->findPaginated($query->page, $query->perPage, $query->city, $query->status);
    }
}
