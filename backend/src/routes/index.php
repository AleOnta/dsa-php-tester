<?php

use Backend\Core\Router;
use Backend\Controllers\RootController;
use Backend\Controllers\UserController;

$router = new Router($container);

# creates routes
$router->get('/', RootController::class, 'index', []);

$router->group('/api/v1', function ($router) {
    $router->get('/users/{id}', RootController::class, 'index', [])->where(['id' => 'int']);

    $router->post('/users/register', UserController::class, 'register', []);
});

$router->dispatch();
