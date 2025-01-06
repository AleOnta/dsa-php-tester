<?php

namespace Backend\Core;

class Route
{
    public string $uri;
    public string $method;
    public string $action;
    public string $pattern;
    public array $parameters;
    public string $controller;
    public array $middlewares;
    private array $constraints;

    public function __construct(string $uri, string $method, string $controller, string $action, array $middlewares)
    {
        $this->uri = $uri;
        $this->method = $method;
        $this->pattern = $this->buildPattern($uri, []);
        $this->controller = $controller;
        $this->action = $action;
        $this->middlewares = $middlewares;
        $this->constraints = [];
    }

    # create a regex pattern of the route
    private function buildPattern(string $uri, array $constraints): string
    {
        $pattern = preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($constraints) {
            $param = $matches[1];
            $constraint = $constraints[$param] ?? '[^/]+';
            return "(?P<{$param}>{$constraint})";
        }, $uri);
        return "#^{$pattern}$#";
    }

    # update the constraints array and rebuild the route pattern
    public function where(array $constraints): self
    {
        $this->constraints = array_merge($this->constraints, $constraints);
        $this->pattern = $this->buildPattern($this->uri, $this->constraints);
        return $this;
    }

    # match the passed uri against the route pattern
    public function matches(string $requestUri)
    {
        return preg_match($this->pattern, $requestUri);
    }

    # extract the parameters from the uri
    public function extractParameters(string $requestUri)
    {
        $this->parameters = [];
        if (preg_match($this->pattern, $requestUri, $matches)) {
            foreach ($matches as $key => $val) {
                if (!is_int($key)) {
                    $this->parameters[$key] = $val;
                }
            }
        }
    }
}
