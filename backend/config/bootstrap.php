<?php

use Backend\Controllers\UserController;
use Backend\Database\Database;
use Backend\Repositories\UserRepository;
use Backend\Services\UserService;
use Backend\Utils\EnvLoader;
use Backend\Utils\AppConstants;
use Backend\Controllers\RootController;

include dirname(__DIR__) . "/vendor/autoload.php";
require_once AppConstants::UTILS_DIR . "helpers.php";

# Initialize App Container
$container = new \Backend\Core\Container;

# registering DB connection into DI container
$container->set('db', fn() => Database::getInstance());

# registering repository classes
$container->set(UserRepository::class, fn($c) => new UserRepository($c->get('db')));

# registering service classes
$container->set(UserService::class, fn($c) => new UserService($c->get(UserRepository::class)));

# registering controller classes
$container->set(RootController::class, fn() => new RootController());
$container->set(UserController::class, fn($c) => new UserController($c->get(UserService::class)));
