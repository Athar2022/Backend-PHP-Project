<?php
namespace App\Core;

final class Router
{
    
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, $handler): void  { $this->routes['GET'][$this->norm($path)]  = $handler; }
    public function post(string $path, $handler): void { $this->routes['POST'][$this->norm($path)] = $handler; }

    public function dispatch(string $method, string $uri): void
    {

        $path = $this->norm(parse_url($uri, PHP_URL_PATH) ?: '/');

        $handler = $this->routes[$method][$path] ?? null;

        if (!$handler) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        if (is_array($handler)) {
            [$class, $action] = $handler;
            (new $class())->$action();
        } else {
            $handler();
        }
    }

    private function norm(string $p): string
    {
        $p = rtrim($p, '/');
        return $p === '' ? '/' : $p;
    }
}
