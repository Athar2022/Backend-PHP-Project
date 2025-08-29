<?php

namespace App\Core;

final class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => []
    ];

    public function get(string $path, $handler): void  { $this->addRoute('GET', $path, $handler); }
    public function post(string $path, $handler): void { $this->addRoute('POST', $path, $handler); }
    public function put(string $path, $handler): void { $this->addRoute('PUT', $path, $handler); }
    public function delete(string $path, $handler): void { $this->addRoute('DELETE', $path, $handler); }

    private function addRoute(string $method, string $path, $handler): void
    {
        $this->routes[$method][$this->norm($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = $this->norm(parse_url($uri, PHP_URL_PATH) ?: '/');

        // Try to find an exact match first
        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            $this->callHandler($handler);
            return;
        }

        // If no exact match, check for dynamic routes
        foreach ($this->routes[$method] as $routePattern => $handler) {
            if (preg_match($this->convertToRegex($routePattern), $path, $matches)) {
                array_shift($matches); // Remove the full match
                $this->callHandler($handler, $matches);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }

    private function callHandler($handler, array $params = []): void
    {
        if (is_array($handler)) {
            [$class, $action] = $handler;
            // Ensure the controller class is fully qualified
            if (!str_starts_with($class, 'App\\Controllers\\')) {
                $class = 'App\\Controllers\\' . $class;
            }
            $controllerInstance = new $class();
            call_user_func_array([$controllerInstance, $action], $params);
        } else if (is_callable($handler)) {
            call_user_func_array($handler, $params);
        } else {
            // Handle 'Controller@method' string format
            [$controllerName, $action] = explode('@', $handler);
            $controllerClass = 'App\\Controllers\\' . $controllerName;
            $controllerInstance = new $controllerClass();
            call_user_func_array([$controllerInstance, $action], $params);
        }
    }

    private function norm(string $p): string
    {
        $p = rtrim($p, '/');
        return $p === '' ? '/' : $p;
    }

    private function convertToRegex(string $path): string
    {
        // Convert {param} to regex capture groups
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $path);
        return '#^' . $regex . '$#';
    }
}

