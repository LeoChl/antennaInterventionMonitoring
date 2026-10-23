<?php

declare(strict_types=1);

namespace App\Domain\Intervention\Exception;

final class InterventionAlreadyClosed extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Intervention already closed');
    }
}
