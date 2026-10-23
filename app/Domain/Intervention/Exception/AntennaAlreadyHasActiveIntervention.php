<?php

declare(strict_types=1);

namespace App\Domain\Intervention\Exception;

final class AntennaAlreadyHasActiveIntervention extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Antenna already has an active intervention');
    }
}
