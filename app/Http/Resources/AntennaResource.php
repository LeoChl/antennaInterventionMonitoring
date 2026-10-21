<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Antenna\Antenna;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AntennaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Antenna $antenna */
        $antenna = $this->resource;

        return [
            'id' => $antenna->id,
            'name' => $antenna->name,
            'city' => $antenna->city,
            'status' => $antenna->status->value,
            'created_at' => $antenna->createdAt->format(DATE_ATOM),
        ];
    }
}
