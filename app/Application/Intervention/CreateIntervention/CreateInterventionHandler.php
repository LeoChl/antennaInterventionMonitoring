<?php

declare(strict_types=1);

namespace App\Application\Intervention\CreateIntervention;

use App\Domain\Antenna\AntennaRepository;
use App\Domain\Antenna\AntennaStatus;
use App\Domain\Intervention\Exception\AntennaAlreadyHasActiveIntervention;
use App\Domain\Intervention\InterventionRepository;
use App\Domain\Shared\TransactionManager;

final class CreateInterventionHandler
{
    public function __construct(
        private readonly InterventionRepository $interventionRepository,
        private readonly AntennaRepository $antennaRepository,
        private readonly TransactionManager $transactionManager,
    ) {}

    public function handle(CreateInterventionCommand $command): int
    {
        return $this->transactionManager->run(function () use ($command): int {
            $antenna = $this->antennaRepository->findAntennaById($command->antennaId);

            if ($antenna->hasActiveIntervention()) {
                throw new AntennaAlreadyHasActiveIntervention;
            }

            $interventionId = $this->interventionRepository->createIntervention(
                $command->antennaId,
                $command->description,
                $command->technicianIdentity,
                $command->priority,
            );

            $this->antennaRepository->updateStatus($antenna->id, AntennaStatus::DOWN);

            return $interventionId;
        });
    }
}
