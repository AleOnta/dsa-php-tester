<?php

use Backend\Utils\EnvLoader;
use Backend\Utils\AppConstants;
use Backend\Controllers\RootController;

include dirname(__DIR__) . "/vendor/autoload.php";
require_once AppConstants::UTILS_DIR . "helpers.php";

# Initialize App Container
$container = new \Backend\Core\Container;

# registering controllers
$container->set(RootController::class, function() {
    return new RootController();
});
