<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Antenna\ListAntennas\ListAntennasQuery;
use Illuminate\Foundation\Http\FormRequest;

final class ListAntennasRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ];
    }

    public function toQuery(): ListAntennasQuery
    {
        return new ListAntennasQuery((int) $this->input('page', 1), (int) $this->input('per_page', 20));
    }
}
