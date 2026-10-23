<?php

declare(strict_types=1);

namespace App\Application\Intervention\CloseIntervention;

use App\Domain\Antenna\AntennaRepository;
use App\Domain\Antenna\AntennaStatus;
use App\Domain\Intervention\Exception\InterventionAlreadyClosed;
use App\Domain\Intervention\Exception\InterventionEndedBeforeStart;
use App\Domain\Intervention\InterventionRepository;
use App\Domain\Shared\TransactionManager;

final class CloseInterventionHandler
{
    public function __construct(
        private readonly InterventionRepository $interventionRepository,
        private readonly AntennaRepository $antennaRepository,
        private readonly TransactionManager $transactionManager,
    ) {}

    public function handle(CloseInterventionCommand $command): void
    {
        $this->transactionManager->run(function () use ($command): void {
            $intervention = $this->interventionRepository->findInterventionById($command->interventionId);

            if (! $intervention->isInterventionOngoing()) {
                throw new InterventionAlreadyClosed;
            }

            $endedAt = $command->endDate ?? new \DateTimeImmutable;

            if ($endedAt < $intervention->createdAt) {
                throw new InterventionEndedBeforeStart;
            }

            $this->interventionRepository->closeIntervention(
                $intervention->id,
                $command->technicianIdentity,
                $command->description,
                $endedAt,
            );

            $this->antennaRepository->updateStatus($intervention->antennaId, AntennaStatus::UP);
        });
    }
}
