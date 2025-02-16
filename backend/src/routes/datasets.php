<?php

use Backend\Services\ApiKeyService;
use Backend\Middlewares\AuthMiddleware;
use Backend\Controllers\DatasetsController;
use Backend\Middlewares\RateLimitApiKeyMiddleware;
use Backend\Middlewares\RateLimitMiddleware;

$router->post(
    '/datasets/upload',
    DatasetsController::class,
    'index',
    [
        # middleware for authentication
        [AuthMiddleware::class, [$container->get(ApiKeyService::class)]],
        # rate limit middleware
        [
            RateLimitApiKeyMiddleware::class,
            [
                'db' => $container->get('db'),
                'endpoint' => '/api/v1/datasets/upload',
                'limit' => 100,
                'window' => 60
            ]
        ]
    ]
);
