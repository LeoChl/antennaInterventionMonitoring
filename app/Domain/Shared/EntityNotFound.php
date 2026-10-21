<?php

declare(strict_types=1);

namespace App\Domain\Shared;

class EntityNotFound extends \DomainException
{
    public function __construct(string $entityName)
    {
        parent::__construct("{$entityName} not found");
    }
}
