<?php

declare(strict_types=1);

namespace App\Domain\Antenna;

use App\Domain\Intervention\Intervention;

final class Antenna
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $city,
        public readonly AntennaStatus $status,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?Intervention $latestIntervention = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city' => $this->city,
            'status' => $this->status->value,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'latestIntervention' => $this->latestIntervention?->toArray(),
        ];
    }

    public static function fromArray(array $data, ?Intervention $latestIntervention = null): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['city'],
            AntennaStatus::from($data['status']),
            new \DateTimeImmutable($data['created_at']),
            $latestIntervention,
        );
    }
}
