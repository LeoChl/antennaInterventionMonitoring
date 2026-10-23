<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Intervention\InterventionPriority;
use App\Infrastructure\Persistence\Eloquent\AntennaModel;
use App\Infrastructure\Persistence\Eloquent\InterventionModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class StoreInterventionTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'secret-token';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.api.token' => self::TOKEN]);
    }

    #[TestDox('Given an antenna without intervention, when createIntervention is called, then it is persisted as ongoing and returned')]
    public function test_creates_intervention_successfully(): void
    {
        $antenna = $this->createAntenna('UP');

        $response = $this->createIntervention($antenna->id);

        $response->assertCreated()->assertJsonStructure(['intervention_id']);
        $this->assertDatabaseHas('antenna_intervention', [
            'id' => $response->json('intervention_id'),
            'antenna_id' => $antenna->id,
            'description' => 'Replace faulty module',
            'technician_identity' => 'John Doe',
            'priority' => InterventionPriority::HIGH->value,
            'ended_at' => null,
        ]);
    }

    #[TestDox('Given an UP antenna, when createIntervention is called, then the antenna becomes DOWN')]
    public function test_marks_antenna_as_down(): void
    {
        $antenna = $this->createAntenna('UP');

        $this->createIntervention($antenna->id)->assertCreated();

        $this->assertDatabaseHas('antenna', ['id' => $antenna->id, 'status' => 'DOWN']);
    }

    #[TestDox('Given an intervention just created and another intervetion try to be created on the same antenna, when createIntervention is called, then it is rejected with a conflict')]
    public function test_rejects_second_intervention_on_same_antenna(): void
    {
        $antenna = $this->createAntenna('UP');
        $this->createIntervention($antenna->id)->assertCreated();

        $this->createIntervention($antenna->id)
            ->assertConflict()
            ->assertJsonPath('message', 'Antenna already has an active intervention');
    }

    #[TestDox('Given an antenna with an ongoing intervention, when createIntervention is called, then it is rejected with a conflict')]
    public function test_rejects_intervention_when_one_is_already_ongoing(): void
    {
        $antenna = $this->createAntenna('DOWN');
        $this->insertIntervention($antenna);

        $this->createIntervention($antenna->id)->assertConflict();

        $antennaInterventions = InterventionModel::where('antenna_id', $antenna->id)->get();
        $this->assertSame(1, $antennaInterventions->count());
    }

    #[TestDox('Given an antenna whose previous intervention is closed, when createIntervention is called, then it is created')]
    public function test_allows_intervention_when_previous_one_is_closed(): void
    {
        $antenna = $this->createAntenna('UP');
        $this->insertIntervention($antenna, '2026-01-01 10:00:00', '2026-01-02 10:00:00');

        $this->createIntervention($antenna->id)->assertCreated();

        $antennaInterventions = InterventionModel::where('antenna_id', $antenna->id)->get();
        $this->assertSame(2, $antennaInterventions->count());
    }

    #[TestDox('Given two antennas with one having an ongoing intervention, when createIntervention is called on the other, then it is created')]
    public function test_ongoing_intervention_does_not_block_other_antennas(): void
    {
        $busyAntenna = $this->createAntenna('DOWN');
        $freeAntenna = $this->createAntenna('UP');
        $this->insertIntervention($busyAntenna);

        $this->createIntervention($freeAntenna->id)->assertCreated();
    }

    #[DataProvider('invalidRequests')]
    #[TestDox('Given an invalid request, when createIntervention is called, then a validation error is returned and nothing is persisted')]
    public function test_rejects_invalid_request(string $antennaId, array $payload, string $field): void
    {
        $this->postIntervention($antennaId, $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors($field);

        $this->assertDatabaseCount('antenna_intervention', 0);
    }

    public static function invalidRequests(): array
    {
        $withoutKey = static fn (string $key): array => array_diff_key(self::validPayload(), [$key => true]);

        return [
            'missing description' => ['1', $withoutKey('description'), 'description'],
            'missing technician_identity' => ['1', $withoutKey('technician_identity'), 'technician_identity'],
            'missing priority' => ['1', $withoutKey('priority'), 'priority'],
            'unknown priority' => ['1', ['priority' => 'URGENT'] + self::validPayload(), 'priority'],
            'lowercase priority' => ['1', ['priority' => 'high'] + self::validPayload(), 'priority'],
            'description over 255 characters' => ['1', ['description' => str_repeat('a', 256)] + self::validPayload(), 'description'],
            'technician_identity over 255 characters' => ['1', ['technician_identity' => str_repeat('a', 256)] + self::validPayload(), 'technician_identity'],
            'non-numeric antenna id' => ['abc', self::validPayload(), 'antenna_id'],
            'zero antenna id' => ['0', self::validPayload(), 'antenna_id'],
            'antenna id above integer max' => ['2147483648', self::validPayload(), 'antenna_id'],
        ];
    }

    private static function validPayload(): array
    {
        return [
            'description' => 'Replace faulty module',
            'technician_identity' => 'John Doe',
            'priority' => InterventionPriority::HIGH->value,
        ];
    }

    private function createAntenna(string $status): AntennaModel
    {
        return AntennaModel::create(['name' => 'Antenna '.uniqid(), 'city' => 'Paris', 'status' => $status]);
    }

    private function insertIntervention(AntennaModel $antenna, string $createdAt = '2026-01-01 10:00:00', ?string $endedAt = null): InterventionModel
    {
        return InterventionModel::create([
            'antenna_id' => $antenna->id,
            'description' => 'Existing',
            'technician_identity' => 'Jane Doe',
            'priority' => InterventionPriority::LOW->value,
            'created_at' => $createdAt,
            'ended_at' => $endedAt,
        ]);
    }

    private function createIntervention(int $antennaId): TestResponse
    {
        return $this->postIntervention((string) $antennaId, self::validPayload());
    }

    private function postIntervention(string $antennaId, array $payload): TestResponse
    {
        return $this->postJson("/api/antennas/{$antennaId}/interventions", $payload, ['Authorization' => 'Bearer '.self::TOKEN]);
    }
}
