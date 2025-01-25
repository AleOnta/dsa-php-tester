<?php

use Backend\Controllers\ApiKeyController;
use Backend\Core\Router;
use Backend\Controllers\RootController;
use Backend\Controllers\UserController;

$router = new Router($container);

# creates routes
$router->get('/', RootController::class, 'index', []);

$router->group('/api/v1', function ($router) {
    # Users
    # register as new user    
    $router->post('/users/register', UserController::class, 'register', []);
    # login and retrieve api key
    $router->post('/auth/login', ApiKeyController::class, 'authenticate', []);
});

$router->dispatch();
