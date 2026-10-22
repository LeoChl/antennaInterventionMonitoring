<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Persistence\Eloquent;

use App\Domain\Antenna\Antenna;
use App\Domain\Antenna\AntennaStatus;
use App\Domain\Intervention\InterventionPriority;
use App\Infrastructure\Persistence\Eloquent\AntennaModel;
use App\Infrastructure\Persistence\Eloquent\EloquentAntennaRepository;
use App\Infrastructure\Persistence\Eloquent\InterventionModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase as TestsTestCase;

class EloquentAntennaTest extends TestsTestCase
{
    use RefreshDatabase;

    private EloquentAntennaRepository $repository;

    private const DEFAULT_PAGE = 1;

    private const DEFAULT_PER_PAGE = 10;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(EloquentAntennaRepository::class);
    }

    #[TestDox('Given no filters are passed, when finding antennas, then it returns the antenna with UP status')]
    public function test_finds_up_antenna(): void
    {
        AntennaModel::create(['name' => 'A1', 'city' => 'Paris', 'status' => 'UP']);
        AntennaModel::create(['name' => 'A2', 'city' => 'Paris', 'status' => 'DOWN']);

        $antennas = $this->repository->findPaginated(self::DEFAULT_PAGE, self::DEFAULT_PER_PAGE);

        $this->assertCount(1, $antennas);
        [$antenna] = $antennas;
        $this->assertSame('A1', $antenna->name);
        $this->assertSame(AntennaStatus::UP, $antenna->status);
    }

    #[TestDox('Given incorrect city, when finding antennas, then it returns an empty array')]
    public function test_finds_up_antenna_with_incorrect_city(): void
    {
        AntennaModel::create(['name' => 'A1', 'city' => 'Paris', 'status' => 'UP']);

        $result = $this->repository->findPaginated(self::DEFAULT_PAGE, self::DEFAULT_PER_PAGE, 'Lyon');

        $this->assertCount(0, $result);
    }

    #[TestDox('Given DOWN status filter is passed, when finding antennas, then it returns the antenna with DOWN status')]
    public function test_finds_down_antenna(): void
    {
        AntennaModel::create(['name' => 'A1', 'city' => 'Paris', 'status' => 'UP']);
        AntennaModel::create(['name' => 'A2', 'city' => 'Paris', 'status' => 'DOWN']);

        $antennas = $this->repository->findPaginated(self::DEFAULT_PAGE, self::DEFAULT_PER_PAGE, null, AntennaStatus::DOWN);

        $this->assertCount(1, $antennas);
        [$antenna] = $antennas;
        $this->assertSame('A2', $antenna->name);
        $this->assertSame(AntennaStatus::DOWN, $antenna->status);
    }

    #[TestDox('Given filter with city and default status (UP), when finding antennas, then it returns the up antenna with that city')]
    public function test_finds_antenna_by_city(): void
    {
        AntennaModel::create(['name' => 'A1', 'city' => 'Montpellier', 'status' => 'UP']);
        AntennaModel::create(['name' => 'A2', 'city' => 'Montpellier', 'status' => 'DOWN']);
        AntennaModel::create(['name' => 'A3', 'city' => 'Paris', 'status' => 'UP']);
        AntennaModel::create(['name' => 'A4', 'city' => 'Paris', 'status' => 'DOWN']);

        $antennas = $this->repository->findPaginated(self::DEFAULT_PAGE, self::DEFAULT_PER_PAGE, 'Paris');

        $this->assertCount(1, $antennas);

        [$antenna] = $antennas;

        $this->assertSame('A3', $antenna->name);
        $this->assertSame(AntennaStatus::UP, $antenna->status);
    }

    #[TestDox('Given filter with city and down status, when finding antennas, then it returns the down antenna with that city')]
    public function test_finds_down_antenna_by_city(): void
    {
        AntennaModel::create(['name' => 'A1', 'city' => 'Montpellier', 'status' => 'UP']);
        AntennaModel::create(['name' => 'A2', 'city' => 'Montpellier', 'status' => 'DOWN']);
        AntennaModel::create(['name' => 'A3', 'city' => 'Paris', 'status' => 'UP']);
        AntennaModel::create(['name' => 'A4', 'city' => 'Paris', 'status' => 'DOWN']);

        $antennas = $this->repository->findPaginated(self::DEFAULT_PAGE, self::DEFAULT_PER_PAGE, 'Paris', AntennaStatus::DOWN);

        $this->assertCount(1, $antennas);

        [$antenna] = $antennas;

        $this->assertSame('A4', $antenna->name);
        $this->assertSame(AntennaStatus::DOWN, $antenna->status);
    }

    #[TestDox('Given an antenna with several interventions, when finding antennas, then it returns only the latest intervention')]
    public function test_finds_latest_intervention_of_antenna(): void
    {
        $mockAntenna = AntennaModel::create(['name' => 'A1', 'city' => 'Paris', 'status' => 'UP']);
        $this->createIntervention($mockAntenna, 'Oldest', '2026-01-01 10:00:00', '2026-01-02 10:00:00');
        $this->createIntervention($mockAntenna, 'Latest', '2026-03-01 10:00:00');
        $this->createIntervention($mockAntenna, 'Middle', '2026-02-01 10:00:00', '2026-02-02 10:00:00');

        [$antenna] = $this->repository->findPaginated(self::DEFAULT_PAGE, self::DEFAULT_PER_PAGE);

        $this->assertSame('Latest', $antenna->latestIntervention->description);
    }

    #[TestDox('Given several antennas with interventions, when finding antennas, then each antenna gets its own latest intervention')]
    public function test_finds_latest_intervention_per_antenna(): void
    {
        $firstMockAntenna = AntennaModel::create(['name' => 'A1', 'city' => 'Paris', 'status' => 'UP']);
        $secondMockAntenna = AntennaModel::create(['name' => 'A2', 'city' => 'Paris', 'status' => 'UP']);
        $this->createIntervention($firstMockAntenna, 'A1 old', '2026-01-01 10:00:00', '2026-01-02 10:00:00');
        $this->createIntervention($firstMockAntenna, 'A1 latest', '2026-02-01 10:00:00');
        $this->createIntervention($secondMockAntenna, 'A2 only', '2026-03-01 10:00:00');

        $result = $this->repository->findPaginated(self::DEFAULT_PAGE, self::DEFAULT_PER_PAGE);

        $this->assertSame(
            ['A1 latest', 'A2 only'],
            array_map(fn (Antenna $antenna) => $antenna->latestIntervention->description, $result),
        );
    }

    #[TestDox('Given an antenna without interventions, when finding antennas, then its latest intervention is null')]
    public function test_finds_no_latest_intervention_when_antenna_has_none(): void
    {
        AntennaModel::create(['name' => 'A1', 'city' => 'Paris', 'status' => 'UP']);

        [$antenna] = $this->repository->findPaginated(self::DEFAULT_PAGE, self::DEFAULT_PER_PAGE);

        $this->assertNull($antenna->latestIntervention);
    }

    #[TestDox('Given an antenna whose latest intervention is finished, when finding antennas, then it maps all intervention fields including endedAt')]
    public function test_maps_finished_latest_intervention(): void
    {
        $mockAntenna = AntennaModel::create(['name' => 'A1', 'city' => 'Paris', 'status' => 'UP']);
        $this->createIntervention($mockAntenna, 'Finished', '2026-01-01 10:00:00', '2026-01-02 12:30:00');

        [$antenna] = $this->repository->findPaginated(self::DEFAULT_PAGE, self::DEFAULT_PER_PAGE);
        $latestIntervention = $antenna->latestIntervention;

        $this->assertEquals(new \DateTimeImmutable('2026-01-02 12:30:00'), $latestIntervention->endedAt);
    }

    private function createIntervention(AntennaModel $antenna, string $description, string $createdAt, ?string $endedAt = null): InterventionModel
    {
        return InterventionModel::create([
            'antenna_id' => $antenna->id,
            'description' => $description,
            'technician_identity' => 'John Doe',
            'priority' => InterventionPriority::HIGH->value,
            'created_at' => $createdAt,
            'ended_at' => $endedAt,
        ]);
    }
}
