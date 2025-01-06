<?php

namespace Backend\Controllers;

class Controller
{

    public function response($code, $responseObj)
    {
        http_response_code($code);
        echo json_encode($responseObj);
        die();
    }
}
