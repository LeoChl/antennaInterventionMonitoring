<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Intervention\CloseIntervention\CloseInterventionCommand;
use Illuminate\Foundation\Http\FormRequest;

final class CloseInterventionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'intervention_id' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'description' => ['required', 'string', 'max:255'],
            'technician_identity' => ['required', 'string', 'max:255'],
            'end_date' => ['nullable', 'date', 'after_or_equal:now'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['intervention_id' => $this->route('interventionId')]);
    }

    public function toCommand(): CloseInterventionCommand
    {
        return new CloseInterventionCommand(
            $this->integer('intervention_id'),
            $this->string('description')->value(),
            $this->string('technician_identity')->value(),
            $this->date('end_date')?->toDateTimeImmutable(),
        );
    }
}
