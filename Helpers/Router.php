<?php

namespace Helpers;

class Router
{
    private $routes = [];
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function get($route, $callback)
    {
        $this->addRoute('GET', $route, $callback);
    }

    public function post($route, $callback)
    {
        $this->addRoute('POST', $route, $callback);
    }

    private function addRoute($method, $route, $callback)
    {
        $this->routes[] = [
            'method' => $method,
            'route' => $route,
            'callback' => $callback
        ];
    }

    public function handleRequest()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $this->logger->info("Request from IP: $ipAddress - Route: $uri - Method: $method");

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['route'] === $uri) {
                call_user_func($route['callback']);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }
}
