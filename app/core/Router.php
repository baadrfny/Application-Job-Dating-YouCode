<?php

namespace core;

class Router
{
    private array $routes = [
        "GET" => [],
        "POST" => []
    ];
    
    private array $middlewares = [];

    public function get(string $path, $callback): void
    {
        $this->routes["GET"][$path] = $callback;
    }

    public function post(string $path, $callback): void
    {
        $this->routes["POST"][$path] = $callback;
    }
    
    public function addMiddleware(string $path, callable $middleware): void
    {
        $this->middlewares[$path] = $middleware;
    }

    public function dispatch(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        
        $request = Request::capture();

        if (!isset($this->routes[$method])) {
            http_response_code(405);
            return "405 Method Not Allowed";
        }

        foreach ($this->routes[$method] as $path => $callback) {

            if ($path !== '/' && substr($path, -1) === '/') {
                $path = rtrim($path, '/');
            }

            $routeRegex = preg_replace_callback('/{\w+(?::([^}]+))?}/', function ($m) {
                return !empty($m[1]) ? '(' . $m[1] . ')' : '([a-zA-Z0-9_-]+)';
            }, $path);

            $routeRegex = '@^' . $routeRegex . '$@';

            if (preg_match($routeRegex, $uri, $matches)) {
                array_shift($matches);
                
                // Execute middleware if exists
                if (isset($this->middlewares[$path])) {
                    $middlewareResult = call_user_func($this->middlewares[$path], $request);
                    if ($middlewareResult !== true) {
                        return (string) $middlewareResult;
                    }
                }

                // Controller@method
                if (is_string($callback) && strpos($callback, '@') !== false) {
                    [$class, $methodName] = explode('@', $callback, 2);
                    $controller = new $class();

                    return (string) call_user_func_array(
                        [$controller, $methodName],
                        array_merge([$request], $matches)
                    );
                }

                // Closure: first arg is Request
                return (string) call_user_func_array(
                    $callback,
                    array_merge([$request], $matches)
                );
            }
        }

        http_response_code(404);
        return "404 Not Found";
    }
}
