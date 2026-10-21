<?php

declare(strict_types=1);

namespace App\Domain\Antenna;

interface AntennaRepository
{
    public function findPaginated(int $page, int $perPage): array;
}
