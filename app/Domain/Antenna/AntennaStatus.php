<?php

declare(strict_types=1);

namespace App\Domain\Antenna;

enum AntennaStatus: string
{
    case UP = 'UP';
    case DOWN = 'DOWN';
}
