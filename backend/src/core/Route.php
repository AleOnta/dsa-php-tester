<?php

namespace Backend\Core;

class Route
{
    public string $uri;
    public string $method;
    public string $action;
    public string $pattern;
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
            $constraint = isset($constraints[$param]) ? $this->regexLookup($constraints[$param]) : '[^/]+';
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
    public function matches(string $requestUri): bool
    {
        return preg_match($this->pattern, $requestUri);
    }

    # extract the parameters from the uri
    public function extractParameters(string $requestUri): array
    {
        $parameters = [];
        if (preg_match($this->pattern, $requestUri, $matches)) {
            foreach ($matches as $key => $val) {
                if (!is_int($key)) {
                    $parameters[$key] = $val;
                }
            }
        }
        return $parameters;
    }

    private function regexLookup(string $label): string
    {
        return match ($label) {
            'int' => '\d+',
            'string' => '[a-zA-Z]+',
            'slug' => '[a-zA-Z0-9-]+',
            'date' => '\d{4}-\d{2}-\d{2}',
            default => '[^/]+'
        };
    }
}
