<?php

namespace Backend\Controllers;

class Controller
{
    protected \Backend\Core\Request $request;

    public function __construct()
    {
        $this->request = new \Backend\Core\Request();
    }

    public function response($code, $responseObj)
    {
        http_response_code($code);
        echo json_encode($responseObj);
        die();
    }
}
