<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Antenna\ListAntennas\ListAntennasHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListAntennasRequest;
use App\Http\Resources\AntennaResource;
use Illuminate\Pagination\LengthAwarePaginator;

final class AntennaController extends Controller
{
    public function __construct(
        private readonly ListAntennasHandler $listAntennasHandler,
    ) {
        parent::__construct();
    }

    public function __invoke(ListAntennasRequest $request)
    {
        $queryParams = $request->toQuery();
        $result = $this->listAntennasHandler->__invoke($queryParams);

        $paginator = (new LengthAwarePaginator(
            $result->items,
            $result->total,
            $result->perPage,
            $result->page,
            ['path' => $request->url()],
        ))->withQueryString();

        return AntennaResource::collection($paginator);
    }
}
