<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Shared\TransactionManager;
use Illuminate\Support\Facades\DB;

final class LaravelTransactionManager implements TransactionManager
{
    public function run(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}
