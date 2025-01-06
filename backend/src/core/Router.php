<?php

namespace Backend\Core;

use Backend\Controllers\RootController;

class Router
{
    protected array $routes = [];
    protected Route $defaultRoute;
    protected Container $c;

    public function addRoute(string $uri, string $method, string $controller, string $action, array $middlewareStack)
    {
        $route = new Route($uri, $method, $controller, $action, $middlewareStack);
        $this->routes[$method][] = $route;
        # allow chaining 
        return $route;
    }

    public function get(string $uri, string $controller, string $action, array $middlewareStack)
    {
        return $this->addRoute($uri, 'GET', $controller, $action, $middlewareStack);
    }

    public function post(string $uri, string $controller, string $action, array $middlewareStack)
    {
        return $this->addRoute($uri, 'POST', $controller, $action, $middlewareStack);
    }

    public function put(string $uri, string $controller, string $action, array $middlewareStack)
    {
        return $this->addRoute($uri, 'PUT', $controller, $action, $middlewareStack);
    }

    public function patch(string $uri, string $controller, string $action, array $middlewareStack)
    {
        return $this->addRoute($uri, 'PATCH', $controller, $action, $middlewareStack);
    }

    public function delete(string $uri, string $controller, string $action, array $middlewareStack)
    {
        return $this->addRoute($uri, 'DELETE', $controller, $action, $middlewareStack);
    }

    public function dispatch()
    {
        # retrieve full uri
        $uri = $_SERVER['REQUEST_URI'];
        # check if method is set in POST (form) or use the request
        $method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
        # loop on all routes of matched method
        foreach ($this->routes[$method] as $route) {
            # check for route pattern
            if ($route->matches($uri)) {
                # extract parameters
                $route->extractParameters($uri);
            }
        }
    }
}
