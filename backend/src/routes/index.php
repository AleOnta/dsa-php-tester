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
    # Users
    # register as new user    
    $router->post('/users/register', UserController::class, 'register', []);
    # login and retrieve api key
    $router->post('/auth/login', ApiKeyController::class, 'authenticate', []);
    # refresh apikey token
    $router->get(
        '/auth/refresh-apikey',
        ApiKeyController::class,
        'refresh',
        [
            [
                RateLimitMiddleware::class,
                [
                    'db' => $container->get('db'),
                    'endpoint' => '/api/v1/auth/refresh-apikey',
                    'limit' => 3,
                    'window' => 900
                ]
            ]
        ]
    );



    # DATASETS ENDPOINTS
    include(AppConstants::ROUTES_DIR . "datasets.php");
});

$router->dispatch();
