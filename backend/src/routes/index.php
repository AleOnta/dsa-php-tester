<?php

use Backend\Controllers\ApiKeyController;
use Backend\Controllers\DatasetsController;
use Backend\Core\Router;
use Backend\Controllers\RootController;
use Backend\Controllers\UserController;
use Backend\Middlewares\AuthMiddleware;
use Backend\Middlewares\RateLimitMiddleware;
use Backend\Services\ApiKeyService;
use Backend\Utils\AppConstants;

$router = new Router($container);

# creates routes
$router->get('/', RootController::class, 'index', []);

$router->group('/api/v1', function ($router, $container) {

    # register as new user    
    $router->post('/users/register', UserController::class, 'register', []);

    # update a user
    $router->patch('/users/edit/{id}', UserController::class, 'update', [
        [AuthMiddleware::class, [$container->get(ApiKeyService::class)]],
        # [RateLimitMiddleware::class, rateLimitSetting($container, '/api/v1/users/edit/{id}')],
    ])->where(['id' => 'int']);

    # login and retrieve api key
    $router->post('/auth/login', ApiKeyController::class, 'authenticate', [
        [RateLimitMiddleware::class, rateLimitSetting($container, '/api/v1/auth/login')],
    ]);

    # refresh apikey token
    $router->get('/auth/refresh-apikey', ApiKeyController::class, 'refresh', [
        [RateLimitMiddleware::class, rateLimitSetting($container, '/api/v1/auth/refresh-apikey', 3, 900)]
    ]);



    # DATASETS ENDPOINTS
    include(AppConstants::ROUTES_DIR . "datasets.php");
});

$router->dispatch();
