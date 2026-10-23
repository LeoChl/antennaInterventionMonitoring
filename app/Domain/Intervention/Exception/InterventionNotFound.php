<?php

declare(strict_types=1);

namespace App\Domain\Intervention\Exception;

use App\Domain\Shared\EntityNotFound;

final class InterventionNotFound extends EntityNotFound
{
    public function __construct()
    {
        parent::__construct('Intervention');
    }
}
