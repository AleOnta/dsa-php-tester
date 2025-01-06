<?php

use Backend\Core\Route;
use Backend\Core\Router;
use Backend\Controllers\RootController;

$router = new Router();

# creates routes
$router->get('/', RootController::class, 'index', []);
$route = $router->get('/users/{id}', RootController::class, 'index', [])->where(['id' => '\d+']);
$router->dispatch();
