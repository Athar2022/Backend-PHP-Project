<?php

// Simple PSR-4-ish autoloader for App\*
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) === 0) {
        $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
        $file = __DIR__ . '/../app/' . $relative . '.php';
        if (file_exists($file)) require $file;
    }
});

// Create router and include all routes
$router = new App\Core\Router();
require __DIR__ . '/../routes/web.php';

// Dispatch current request
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
