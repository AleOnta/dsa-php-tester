<?php

namespace Backend\Core;

use Backend\Controllers\RootController;
use Closure;

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
        # add prefix if present
        $uri = $this->groupPrefix . $uri;
        # create the route object
        $route = new Route($uri, $method, $controller, $action, $middlewareStack);
        # store the route in the router
        $this->routes[$method][] = $route;
        # allow chaining 
        return $route;
    }

    # create a group of routes under the same prefix
    public function group(string $prefix, callable $callback)
    {
        $previousPrefix = $this->groupPrefix;
        $this->groupPrefix = $prefix;
        $callback($this, $this->c);
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
                    # execute middlewares
                    $this->executeMiddlewares($route->middlewares, function () use ($controller, $action, $params) {
                        # execute requested method 
                        call_user_func_array([$controller, $action], array_values($params));
                    });
                } else {
                    # handle undefined request
                    $this->handle404($uri, $route->controller, $route->action);
                }
            }
        }
        # handle undefined request
        dd("URI: {$uri} not found...");
    }

    private function executeMiddlewares(array $middlewareStack, Closure $next): void
    {
        # check if there are middlewares to execute
        if (count($middlewareStack) === 0) {
            # no middlewares - proceed with request
            $next();
            return;
        }
        # get the first middleware in the stack
        $middlewareInfo = array_shift($middlewareStack);
        # create and instance of the middleware class
        if (is_array($middlewareInfo)) {
            [$middlewareClass, $args] = $middlewareInfo;
            $middleware = new $middlewareClass(...$args);
        } else {
            $middlewareClass = $middlewareInfo;
            $middleware = new $middlewareClass();
        }

        # execute the middlewares until the middleware stack is empty
        $middleware->handle($_SERVER, function () use ($middlewareStack, $next) {
            $this->executeMiddlewares($middlewareStack, $next);
        });
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
