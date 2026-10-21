<?php

declare(strict_types=1);

namespace App\Domain\Antenna;

final class Antenna
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $city,
        public readonly AntennaStatus $status,
        public readonly \DateTimeImmutable $createdAt,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city' => $this->city,
            'status' => $this->status->value,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }

    public function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['city'],
            AntennaStatus::from($data['status']),
            new \DateTimeImmutable($data['created_at']),
        );
    }
}
