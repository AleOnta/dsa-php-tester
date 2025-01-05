<?php

namespace Backend\Core;

class Request {

    public array $headers;
    public string $method;
    public string $uri;
    public array $queryParameters;
    public ?array $body;

    public function __construct() 
    {
        # extract all headers from HTTP req.
        $this->headers = getallheaders();
        # retrieve the HTTP method from req.
        $this->method = $_SERVER['REQUEST_METHOD'];
        # extract full requested URI
        $this->uri = $_SERVER['REQUEST_URI'];
        # retrieve GET vars (query params)
        $this->queryParameters = $_GET;
        # parse req. body if present
        $this->parseRequestBody();
    }

    private function parseRequestBody(): void 
    {
        # set json as default content type
        $body = ['format' => 'json'];
        # check for Content-Type definition
        if (isset($this->headers['Content-Type'])) {
            $body['format'] = match ($this->headers['Content-Type']) {
                'application/xml' => 'xml',
                'application/json' => 'json',
                default => 'not supported'
            };
        }

        # not handling anything except json for now 
        if ($body['format'] !== 'json') {
            $this->body = $body;
        }

        switch ($body['format']) {
            case 'json':
                # read the request body
                $content = file_get_contents('php://input');
                # decode the json body and set it in the body var
                $body['content'] = json_decode($content, 1);
                break;
            default:
                # TODO: handle not supported body content type
                $body['content'] = false;
        }
        # assign body attr
        $this->body = $body;
    }

}