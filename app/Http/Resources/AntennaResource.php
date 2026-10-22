<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Antenna\Antenna;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Antenna
 */
class AntennaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city' => $this->city,
            'status' => $this->status->value,
            'created_at' => $this->createdAt->format(DATE_ATOM),
        ];
    }
}
