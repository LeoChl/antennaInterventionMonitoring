<?php

declare(strict_types=1);

namespace App\Application\Antenna\ListAntennas;

final class ListAntennasQuery
{
    public function __construct(
        public readonly int $page,
        public readonly int $perPage,
    ) {}

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];
    }
}
