<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Antenna\AntennaStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AntennaSeeder extends Seeder
{
    private const ANTENNAS_TO_INSERT = 100_000;

    private const INSERT_CHUNK_SIZE = 20_000;

    private const PERCENT_DOWN_ANTENNAS_PROBABILITY = 20;

    private const CITIES = [
        'Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg',
        'Montpellier', 'Bordeaux', 'Lille', 'Rennes', 'Limoges', 'Grenoble', 'Dijon',
    ];

    public function run(): void
    {
        foreach (array_chunk(range(1, self::ANTENNAS_TO_INSERT), self::INSERT_CHUNK_SIZE) as $indexes) {
            DB::table('antenna')->insert($this->generateMockAntennas($indexes));
        }
    }

    private function generateMockAntennas(array $indexes): array
    {
        return array_map(function (int $index): array {
            $status = random_int(1, 100) <= self::PERCENT_DOWN_ANTENNAS_PROBABILITY ? AntennaStatus::DOWN->value : AntennaStatus::UP->value;

            return [
                'name' => sprintf('antenna-%06d', $index),
                'city' => self::CITIES[array_rand(self::CITIES)],
                'status' => $status,
            ];
        }, $indexes);
    }
}
