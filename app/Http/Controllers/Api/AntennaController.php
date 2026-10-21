<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Antenna\ListAntennas\ListAntennasHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListAntennasRequest;
use App\Http\Resources\AntennaResource;

final class AntennaController extends Controller
{
    public function __construct(
        private readonly ListAntennasHandler $listAntennasHandler,
    ) {
        parent::__construct();
    }

    public function __invoke(ListAntennasRequest $request)
    {
        $antennas = $this->listAntennasHandler->__invoke($request->toQuery());

        return AntennaResource::collection($antennas);
    }
}
