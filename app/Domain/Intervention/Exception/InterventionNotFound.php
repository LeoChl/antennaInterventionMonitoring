<?php

declare(strict_types=1);

namespace App\Domain\Intervention\Exception;

final class InterventionNotFound extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Intervention not found');
    }
}
