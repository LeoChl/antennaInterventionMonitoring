<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Domain\Antenna\AntennaRepository;
use App\Domain\Intervention\InterventionRepository;
use App\Domain\Shared\TransactionManager;
use App\Infrastructure\Persistence\Eloquent\EloquentAntennaRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentInterventionRepository;
use App\Infrastructure\Persistence\LaravelTransactionManager;
use Illuminate\Support\ServiceProvider;

final class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AntennaRepository::class,
            EloquentAntennaRepository::class
        );

        $this->app->bind(
            InterventionRepository::class,
            EloquentInterventionRepository::class
        );

        $this->app->bind(
            TransactionManager::class,
            LaravelTransactionManager::class
        );
    }
}
