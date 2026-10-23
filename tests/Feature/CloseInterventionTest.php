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

final class CloseInterventionTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'secret-token';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.api.token' => self::TOKEN]);
    }

    #[TestDox('Given an ongoing intervention, when closeIntervention is called, then it is ended with the closing details')]
    public function test_closes_ongoing_intervention(): void
    {
        $intervention = $this->insertIntervention($this->createAntenna('DOWN'));

        $this->closeIntervention((string) $intervention->id)
            ->assertOk()
            ->assertJsonPath('message', 'Intervention closed');

        $this->assertDatabaseHas('antenna_intervention', [
            'id' => $intervention->id,
            'description' => 'Module replaced',
            'technician_identity' => 'John Doe',
        ]);
        $this->assertNotNull(InterventionModel::find($intervention->id)->ended_at);
    }

    #[TestDox('Given an ongoing intervention and an end_date, when closeIntervention is called, then ended_at uses the given date')]
    public function test_closes_intervention_with_given_end_date(): void
    {
        $intervention = $this->insertIntervention($this->createAntenna('DOWN'));

        $this->closeIntervention((string) $intervention->id, self::validPayload() + ['end_date' => '2026-01-03T10:00:00+00:00'])->assertOk();

        $this->assertSame('2026-01-03 10:00:00', InterventionModel::find($intervention->id)->ended_at->utc()->format('Y-m-d H:i:s'));
    }

    #[TestDox('Given an end_date before the intervention started, when closeIntervention is called, then it is rejected and nothing changes')]
    public function test_rejects_end_date_before_intervention_start(): void
    {
        $antenna = $this->createAntenna('DOWN');
        $intervention = $this->insertIntervention($antenna, '2026-01-02 10:00:00');

        $this->closeIntervention((string) $intervention->id, self::validPayload() + ['end_date' => '2026-01-01T10:00:00+00:00'])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Intervention cannot end before it started');

        $this->assertNull(InterventionModel::find($intervention->id)->ended_at);
        $this->assertDatabaseHas('antenna', ['id' => $antenna->id, 'status' => 'DOWN']);
    }

    #[TestDox('Given an ongoing intervention on a DOWN antenna, when closeIntervention is called, then the antenna becomes UP')]
    public function test_marks_antenna_as_up(): void
    {
        $antenna = $this->createAntenna('DOWN');
        $intervention = $this->insertIntervention($antenna);

        $this->closeIntervention((string) $intervention->id)->assertOk();

        $this->assertDatabaseHas('antenna', ['id' => $antenna->id, 'status' => 'UP']);
    }

    #[TestDox('Given an unknown intervention, when closeIntervention is called, then it is rejected with not found')]
    public function test_rejects_unknown_intervention(): void
    {
        $this->closeIntervention('999999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Intervention not found');
    }

    #[TestDox('Given an already closed intervention, when closeIntervention is called, then it is rejected with a conflict and nothing changes')]
    public function test_rejects_already_closed_intervention(): void
    {
        $antenna = $this->createAntenna('UP');
        $intervention = $this->insertIntervention($antenna, '2026-01-01 10:00:00', '2026-01-02 10:00:00');

        $this->closeIntervention((string) $intervention->id)
            ->assertConflict()
            ->assertJsonPath('message', 'Intervention already closed');

        $this->assertDatabaseHas('antenna_intervention', ['id' => $intervention->id, 'description' => 'Existing']);
    }

    #[TestDox('Given two antennas, when one intervention is closed, then the other antenna is untouched')]
    public function test_closing_does_not_affect_other_antennas(): void
    {
        $otherAntenna = $this->createAntenna('DOWN');
        $this->insertIntervention($otherAntenna);
        $intervention = $this->insertIntervention($this->createAntenna('DOWN'));

        $this->closeIntervention((string) $intervention->id)->assertOk();

        $this->assertDatabaseHas('antenna', ['id' => $otherAntenna->id, 'status' => 'DOWN']);
        $this->assertNull(InterventionModel::where('antenna_id', $otherAntenna->id)->first()->ended_at);
    }

    #[DataProvider('invalidRequests')]
    #[TestDox('Given an invalid request, when closeIntervention is called, then a validation error is returned')]
    public function test_rejects_invalid_request(string $interventionId, array $payload, string $field): void
    {
        $this->closeIntervention($interventionId, $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors($field);
    }

    public static function invalidRequests(): array
    {
        $withoutKey = static fn (string $key): array => array_diff_key(self::validPayload(), [$key => true]);

        return [
            'missing description' => ['1', $withoutKey('description'), 'description'],
            'missing technician_identity' => ['1', $withoutKey('technician_identity'), 'technician_identity'],
            'description over 255 characters' => ['1', ['description' => str_repeat('a', 256)] + self::validPayload(), 'description'],
            'technician_identity over 255 characters' => ['1', ['technician_identity' => str_repeat('a', 256)] + self::validPayload(), 'technician_identity'],
            'invalid end_date' => ['1', ['end_date' => 'not-a-date'] + self::validPayload(), 'end_date'],
            'future end_date' => ['1', ['end_date' => '2999-01-01T00:00:00+00:00'] + self::validPayload(), 'end_date'],
            'non-numeric intervention id' => ['abc', self::validPayload(), 'intervention_id'],
            'zero intervention id' => ['0', self::validPayload(), 'intervention_id'],
            'intervention id above integer max' => ['2147483648', self::validPayload(), 'intervention_id'],
        ];
    }

    private static function validPayload(): array
    {
        return [
            'description' => 'Module replaced',
            'technician_identity' => 'John Doe',
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

    private function closeIntervention(string $interventionId, ?array $payload = null): TestResponse
    {
        return $this->patchJson("/api/interventions/{$interventionId}", $payload ?? self::validPayload(), ['Authorization' => 'Bearer '.self::TOKEN]);
    }
}
