<?php

declare(strict_types=1);

namespace App\Domain\Antenna\Exception;

use App\Domain\Shared\EntityNotFound;

final class AntennaNotFound extends EntityNotFound
{
    public function __construct()
    {
        parent::__construct('Antenna');
    }
}
