<?php

# import the config bootstrap file
require_once dirname(__DIR__) . '/config/bootstrap.php';

# setting up CORS policy
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: false");
header("Access-Control-Max-Age: 3600");

# Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    # Preflight request, respond successfully
    http_response_code(200);
    exit();
}

# import routes
require \Backend\Utils\AppConstants::ROUTES_DIR . 'index.php';
