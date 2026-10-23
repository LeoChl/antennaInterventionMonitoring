<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Intervention\CreateIntervention\CreateInterventionCommand;
use App\Domain\Intervention\InterventionPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreInterventionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'antenna_id' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'description' => ['required', 'string', 'max:255'],
            'technician_identity' => ['required', 'string', 'max:255'],
            'priority' => ['required', Rule::enum(InterventionPriority::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['antenna_id' => $this->route('antennaId')]);
    }

    public function toCommand(): CreateInterventionCommand
    {
        return new CreateInterventionCommand(
            $this->integer('antenna_id'),
            $this->string('description')->value(),
            $this->string('technician_identity')->value(),
            $this->enum('priority', InterventionPriority::class),
        );
    }
}
