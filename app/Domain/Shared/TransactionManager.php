<?php

declare(strict_types=1);

namespace App\Domain\Shared;

interface TransactionManager
{
    public function run(callable $callback): void;
}
