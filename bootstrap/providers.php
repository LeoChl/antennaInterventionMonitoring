<?php

declare(strict_types=1);

use App\Infrastructure\Providers\DomainServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    DomainServiceProvider::class,
];
