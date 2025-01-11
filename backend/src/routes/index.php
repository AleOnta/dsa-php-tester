<?php

use Backend\Core\Router;
use Backend\Controllers\RootController;
use Backend\Middlewares\TestMiddleware;

$router = new Router($container);

# creates routes
$router->get('/', RootController::class, 'index', []);

$router->group('/api/v1', function ($router) {
    $router->get('/users/{id}', RootController::class, 'index', [TestMiddleware::class])->where(['id' => 'int']);
    $router->get('/users/{name}', RootController::class, 'index', [TestMiddleware::class])->where(['name' => 'string']);
});

$router->dispatch();
