<?php

declare(strict_types=1);

use App\Domain\Intervention\Exception\AntennaAlreadyHasActiveIntervention;
use App\Domain\Intervention\Exception\InterventionAlreadyClosed;
use App\Domain\Intervention\Exception\InterventionEndedBeforeStart;
use App\Domain\Shared\EntityNotFound;
use App\Http\Middleware\EnsureApiTokenIsValid;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api.token' => EnsureApiTokenIsValid::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
        $exceptions->render(fn (EntityNotFound $e) => response()->json(['message' => $e->getMessage()], 404));
        $exceptions->render(fn (AntennaAlreadyHasActiveIntervention|InterventionAlreadyClosed $e) => response()->json(['message' => $e->getMessage()], 409));
        $exceptions->render(fn (InterventionEndedBeforeStart $e) => response()->json(['message' => $e->getMessage()], 422));
    })->create();
