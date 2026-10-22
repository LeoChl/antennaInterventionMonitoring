<?php

declare(strict_types=1);

namespace App\Domain\Antenna;

use App\Domain\Shared\PaginatedResult;

interface AntennaRepository
{
    public function findPaginated(int $page, int $perPage, ?string $city = null, ?AntennaStatus $status = null): PaginatedResult;
}
