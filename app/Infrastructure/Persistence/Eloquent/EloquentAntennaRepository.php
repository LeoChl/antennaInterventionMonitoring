<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Antenna\Antenna;
use App\Domain\Antenna\AntennaRepository;

final class EloquentAntennaRepository implements AntennaRepository
{
    public function __construct(
        private readonly AntennaModel $model,
    ) {}

    public function findPaginated(int $page, int $perPage): array
    {
        $models = $this->model->paginate($perPage, ['*'], 'page', $page);

        return array_map(
            fn (AntennaModel $m) => Antenna::fromArray($m->toArray()),
            $models->items()
        );
    }
}
