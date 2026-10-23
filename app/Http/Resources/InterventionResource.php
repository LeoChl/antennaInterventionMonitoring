<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Intervention\Intervention;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Intervention
 */
final class InterventionResource extends JsonResource
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'antennaId' => $this->antennaId,
            'description' => $this->description,
            'technicianIdentity' => $this->technicianIdentity,
            'priority' => $this->priority->value,
            'isInterventionOngoing' => $this->isInterventionOngoing(),
            'createdAt' => $this->createdAt->format(self::DATE_FORMAT),
            'endedAt' => $this->endedAt?->format(self::DATE_FORMAT),
        ];
    }
}
