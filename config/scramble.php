<?php

declare(strict_types=1);

use Dedoc\Scramble\SecurityDocumentation\MiddlewareAuthSecurityStrategy;
use Dedoc\Scramble\Support\Generator\SecurityScheme;

return [
    'info' => [
        'version' => env('API_VERSION', '1.0.0'),
        'description' => 'REST API used to monitor antennas and manage maintenance interventions. '
            .'Write operations (GET, POST, PATCH) require a `Authorization: Bearer <API_TOKEN>` header.',
    ],

    'security_strategy' => [
        MiddlewareAuthSecurityStrategy::class,
        [
            'middleware' => ['api.token'],
            'scheme' => SecurityScheme::http('bearer'),
        ],
    ],
];
