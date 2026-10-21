<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Domain\Antenna\AntennaRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentAntennaRepository;
use Illuminate\Support\ServiceProvider;

final class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AntennaRepository::class,
            EloquentAntennaRepository::class
        );
    }
}
