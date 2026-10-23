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

final class ListAntennasTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/api/antennas';

    private const TOKEN = 'secret-token';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.api.token' => self::TOKEN]);
    }

    #[TestDox('Given no token, when listing antennas, then the request is rejected')]
    public function test_rejects_request_without_token(): void
    {
        $this->getJson(self::URL)->assertUnauthorized();
    }

    #[TestDox('Given an antenna without intervention, when listing antennas, then antenna is returned with null latestIntervention')]
    public function test_returns_antenna_without_intervention(): void
    {
        $antenna = $this->createAntenna('A1', 'Paris', 'UP');

        $this->authenticatedGet(self::URL)
            ->assertJsonPath('data', [[
                'id' => $antenna->id,
                'name' => 'A1',
                'city' => 'Paris',
                'status' => 'UP',
                'latestIntervention' => null,
            ]]);
    }

    #[TestDox('Given an antenna with an ongoing intervention, when listing antennas, then antenna is returned with latest intervention')]
    public function test_returns_latest_intervention(): void
    {
        $antenna = $this->createAntenna('A1', 'Paris', 'UP');
        $this->createIntervention($antenna, 'Old', '2026-01-01 10:00:00', '2026-01-02 10:00:00');
        $this->createIntervention($antenna, 'Latest', '2026-02-01 10:00:00');

        $this->authenticatedGet(self::URL)
            ->assertJsonPath('data.0.latestIntervention.description', 'Latest')
            ->assertJsonPath('data.0.latestIntervention.isInterventionOngoing', true);
    }

    #[TestDox('Given UP and DOWN antennas, when no status is given, then antennas of every status are returned')]
    public function test_returns_every_status_by_default(): void
    {
        $this->createAntenna('A1', 'Paris', 'UP');
        $this->createAntenna('A2', 'Paris', 'DOWN');

        $this->authenticatedGet(self::URL)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'A1')
            ->assertJsonPath('data.1.name', 'A2');
    }

    #[TestDox('Given UP and DOWN antennas, when status=UP is given, then only UP antennas are returned')]
    public function test_filters_by_up_status(): void
    {
        $this->createAntenna('A1', 'Paris', 'UP');
        $this->createAntenna('A2', 'Paris', 'DOWN');

        $this->authenticatedGet(self::URL.'?status=UP')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'A1');
    }

    #[TestDox('Given antennas in several cities, when city and status are given, then only matching antennas are returned')]
    public function test_filters_by_city_and_status(): void
    {
        $this->createAntenna('A1', 'Paris', 'UP');
        $this->createAntenna('A2', 'Paris', 'DOWN');
        $this->createAntenna('A3', 'Lyon', 'DOWN');

        $this->authenticatedGet(self::URL.'?city=Paris&status=DOWN')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'A2');
    }

    #[TestDox('Given more antennas than the page size, when requesting page 2, then the right slice and pagination metadata are returned')]
    public function test_paginates_results(): void
    {
        foreach (['A1', 'A2', 'A3', 'A4', 'A5'] as $name) {
            $this->createAntenna($name, 'Paris', 'UP');
        }

        $this->authenticatedGet(self::URL.'?per_page=2&page=2')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'A3')
            ->assertJsonPath('meta.last_page', 3);
    }

    #[TestDox('Given no antennas, when listing antennas, then an empty list is returned')]
    public function test_returns_empty_list(): void
    {
        $this->authenticatedGet(self::URL)
            ->assertJsonPath('data', []);
    }

    #[TestDox('Given UP and DOWN antennas, when status=DOWN is given, then only DOWN antennas are returned')]
    public function test_filters_by_status(): void
    {
        $this->createAntenna('A1', 'Paris', 'UP');
        $this->createAntenna('A2', 'Paris', 'DOWN');

        $this->authenticatedGet(self::URL.'?status=DOWN')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'A2')
            ->assertJsonPath('data.0.status', 'DOWN');
    }

    #[TestDox('Given filters and pagination, when listing antennas, then the pagination links keep the query string')]
    public function test_pagination_links_keep_filters(): void
    {
        foreach (['A1', 'A2', 'A3'] as $name) {
            $this->createAntenna($name, 'Paris', 'DOWN');
        }

        $nextLink = $this->authenticatedGet(self::URL.'?status=DOWN&per_page=2')
            ->json('links.next');

        $this->assertStringContainsString('status=DOWN', $nextLink);
        $this->assertStringContainsString('per_page=2', $nextLink);
    }

    #[DataProvider('invalidQueries')]
    #[TestDox('Given an invalid query parameter, when listing antennas, then a validation error is returned')]
    public function test_rejects_invalid_query(string $query, string $field): void
    {
        $this->authenticatedGet(self::URL.'?'.$query)
            ->assertUnprocessable()
            ->assertJsonValidationErrors($field);
    }

    public static function invalidQueries(): array
    {
        return [
            'per_page above 100' => ['per_page=101', 'per_page'],
            'unknown status' => ['status=foo', 'status'],
            'lowercase status' => ['status=up', 'status'],
        ];
    }

    private function authenticatedGet(string $url): TestResponse
    {
        return $this->getJson($url, ['Authorization' => 'Bearer '.self::TOKEN]);
    }

    private function createAntenna(string $name, string $city, string $status): AntennaModel
    {
        return AntennaModel::create(['name' => $name, 'city' => $city, 'status' => $status]);
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
