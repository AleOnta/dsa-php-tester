<?php

use Backend\Core\Router;
use Backend\Controllers\RootController;

$router = new Router($container);

# creates routes
$router->get('/', RootController::class, 'index', []);

$router->group('/api/v1', function ($router) {
    $router->get('/users/register', \Backend\Controllers\UserController::class, 'register', []);
    $router->get('/users/{id}', RootController::class, 'index', [])->where(['id' => 'int']);
    $router->get('/users/{name}', RootController::class, 'index', [])->where(['name' => 'string']);
});

$router->dispatch();
