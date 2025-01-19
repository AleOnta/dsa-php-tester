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

    protected function checkRequestBodyParameters(array $requiredParams)
    {
        # extract the request body
        $body = $this->request->body['content'];
        # check for any missing params
        $missing = [];
        foreach ($requiredParams as $required) {
            if (!isset($body[$required])) {
                $missing = [$required => "Parameter '{$required}' is required."];
            }
        }
        return $missing;
    }
}
