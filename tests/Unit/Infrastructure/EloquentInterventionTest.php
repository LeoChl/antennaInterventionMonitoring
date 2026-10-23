<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure;

use App\Domain\Intervention\Exception\AntennaAlreadyHasActiveIntervention;
use App\Domain\Intervention\InterventionPriority;
use App\Infrastructure\Persistence\Eloquent\AntennaModel;
use App\Infrastructure\Persistence\Eloquent\EloquentInterventionRepository;
use App\Infrastructure\Persistence\Eloquent\InterventionModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class EloquentInterventionTest extends TestCase
{
    use RefreshDatabase;

    private EloquentInterventionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new EloquentInterventionRepository;
    }

    #[TestDox('Given an antenna without intervention, when createIntervention is called, then its id is returned and the row is ongoing')]
    public function test_creates_intervention(): void
    {
        $antenna = $this->createAntenna();

        $interventionId = $this->repository->createIntervention($antenna->id, 'Desc', 'John Doe', InterventionPriority::HIGH);

        $this->assertDatabaseHas('antenna_intervention', [
            'id' => $interventionId,
            'antenna_id' => $antenna->id,
            'priority' => InterventionPriority::HIGH->value,
            'ended_at' => null,
        ]);
    }

    #[TestDox('Given an ongoing intervention and another intervention on the same antenna is created, when createIntervention is called, then the unique index violation is translated to a domain exception')]
    public function test_translates_unique_violation_to_domain_exception(): void
    {
        $antenna = $this->createAntenna();
        $this->repository->createIntervention($antenna->id, 'First', 'John Doe', InterventionPriority::LOW);

        $this->expectException(AntennaAlreadyHasActiveIntervention::class);

        $this->repository->createIntervention($antenna->id, 'Second', 'Jane Doe', InterventionPriority::HIGH);
    }

    #[TestDox('Given a closed intervention and another intervention on the same antenna is created, when createIntervention is called, then the unique index does not block it')]
    public function test_allows_new_intervention_after_previous_is_closed(): void
    {
        $antenna = $this->createAntenna();
        InterventionModel::create([
            'antenna_id' => $antenna->id,
            'description' => 'Old',
            'technician_identity' => 'John Doe',
            'priority' => InterventionPriority::LOW->value,
            'created_at' => '2026-01-01 10:00:00',
            'ended_at' => '2026-01-02 10:00:00',
        ]);

        $this->repository->createIntervention($antenna->id, 'New', 'Jane Doe', InterventionPriority::HIGH);

        $this->assertSame(2, InterventionModel::where('antenna_id', $antenna->id)->count());
    }

    private function createAntenna(): AntennaModel
    {
        return AntennaModel::create(['name' => 'Antenna', 'city' => 'Paris', 'status' => 'UP']);
    }
}
