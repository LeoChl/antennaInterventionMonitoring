<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Intervention\CloseIntervention\CloseInterventionHandler;
use App\Application\Intervention\CreateIntervention\CreateInterventionHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInterventionRequest;
use Illuminate\Http\JsonResponse;

final class InterventionController extends Controller
{
    public function __construct(
        private readonly CreateInterventionHandler $createInterventionHandler,
        private readonly CloseInterventionHandler $closeInterventionHandler,
    ) {
        parent::__construct();
    }

    public function createIntervention(StoreInterventionRequest $request): JsonResponse
    {
        $interventionId = $this->createInterventionHandler->handle($request->toCommand());

        return response()->json(['intervention_id' => $interventionId], 201);
    }

    public function closeIntervention() {}
}
