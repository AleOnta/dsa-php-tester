<?php

use Backend\Controllers\ApiKeyController;
use Backend\Database\Database;
use Backend\Utils\AppConstants;
use Backend\Services\UserService;
use Backend\Controllers\UserController;
use Backend\Controllers\RootController;
use Backend\Repositories\UserRepository;
use Backend\Exceptions\ExceptionsHandler;
use Backend\Repositories\ApiKeyRepository;
use Backend\Services\ApiKeyService;

include dirname(__DIR__) . "/vendor/autoload.php";
require_once AppConstants::UTILS_DIR . "helpers.php";

# register error function
ini_set('display_errors', 0);
register_shutdown_function('fatal_error_shutdown');

# register exception handler
$exceptionHandler = new ExceptionsHandler();
set_exception_handler([$exceptionHandler, 'handle']);

# Initialize App Container
$container = new \Backend\Core\Container;

# registering DB connection into DI container
$container->set('db', fn() => Database::getInstance());

# registering repository classes
$container->set(UserRepository::class, fn($c) => new UserRepository($c->get('db')));
$container->set(ApiKeyRepository::class, fn($c) => new ApiKeyRepository($c->get('db')));

# registering service classes
$container->set(UserService::class, fn($c) => new UserService($c->get(UserRepository::class)));
$container->set(ApiKeyService::class, fn($c) => new ApiKeyService($c->get(ApiKeyRepository::class)));

# registering controller classes
$container->set(RootController::class, fn() => new RootController());
$container->set(UserController::class, fn($c) => new UserController($c->get(UserService::class)));
$container->set(ApiKeyController::class, fn($c) => new ApiKeyController($c->get(ApiKeyService::class), $c->get(UserService::class)));
