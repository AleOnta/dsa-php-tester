<?php

namespace Backend\Core;

use Backend\Controllers\RootController;

class Router
{
    protected Container $c;
    public array $routes = [];
    protected Route $defaultRoute;
    protected string $groupPrefix = '';

    public function __construct(Container $container)
    {
        $this->c = $container;
    }

    public function addRoute(string $uri, string $method, string $controller, string $action, array $middlewareStack)
    {
        $route = new Route($uri, $method, $controller, $action, $middlewareStack);
        $this->routes[$method][] = $route;
        # allow chaining 
        return $route;
    }

    # create a group of routes under the same prefix
    public function group(string $prefix, callable $callback)
    {
        $previousPrefix = $this->groupPrefix;
        $this->groupPrefix = $prefix;
        $callback($this);
        $this->groupPrefix = $previousPrefix;
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
                $params = $route->extractParameters($uri);
                # retrieve controller and action from the container
                $controller = $this->c->get($route->controller);
                $action = $route->action;
                if (method_exists($controller, $action)) {
                    # execute requested method 
                    $controller->$action();
                } else {
                    # handle undefined request
                    $this->handle404($uri, $route->controller, $route->action);
                }
            }
        }
    }

    private function handle404(string $uri, string $controller, string $action)
    {
        http_response_code(404);
        $response = [
            'code' => 404,
            'status' => 'not found',
            'payload' => [
                'uri' => $uri,
                'controller' => $controller,
                'action' => $action
            ]
        ];
        echo json_encode($response);
        die();
    }
}
