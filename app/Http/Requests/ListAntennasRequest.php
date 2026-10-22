<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Antenna\ListAntennas\ListAntennasQuery;
use Illuminate\Foundation\Http\FormRequest;

final class ListAntennasRequest extends FormRequest
{
    private const DEFAULT_PAGE = 1;

    private const DEFAULT_PER_PAGE = 20;

    public function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'city' => 'sometimes|string',
            'status' => 'sometimes|string|in:up,down',
        ];
    }

    public function toQuery(): ListAntennasQuery
    {
        $pageInput = $this->input('page', self::DEFAULT_PAGE);
        $perPageInput = $this->input('per_page', self::DEFAULT_PER_PAGE);
        $city = $this->input('city');
        $status = $this->input('status');

        return new ListAntennasQuery((int) $pageInput, (int) $perPageInput, $city, $status);
    }
}
