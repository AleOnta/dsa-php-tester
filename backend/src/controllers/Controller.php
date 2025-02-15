<?php

namespace Backend\Controllers;

use Backend\Exceptions\MissingParameterException;

class Controller
{
    protected \Backend\Core\Request $request;

    public function __construct()
    {
        $this->request = new \Backend\Core\Request();
    }

    public function response($code, $responseObj)
    {
        header("Content-type: application/json; charset=utf-8");
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

        # if any required parameter is missing, return errors
        if (count($missing) > 0) {
            throw new MissingParameterException('Missing parameters', $missing);
        }
    }

    protected function checkPossibleRequestBodyParameters(array $params)
    {
        # extract the request body
        $body = $this->request->body['content'];
        # loop on the body values
        $available = 0;
        foreach ($params as $key) {
            if (isset($body[$key])) {
                if (trim($body[$key]) !== '') {
                    $available++;
                }
            }
        }
        # if not a single parameter is found, return error
        if ($available === 0) {
            throw new MissingParameterException('No parameter received. [' . implode(', ', $params) . ']');
        }
    }
}
