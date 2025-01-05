<?php

include dirname(__DIR__)."/vendor/autoload.php";
include_once dirname(__DIR__)."/src/utils/helpers.php"; 

use Backend\Core\Request;

$req = new Request();
echo json_encode($req->body, JSON_PRETTY_PRINT);