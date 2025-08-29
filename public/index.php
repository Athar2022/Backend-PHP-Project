<?php
// تحميل Composer autoload (PSR-4)
require __DIR__ . '/../vendor/autoload.php';

// إنشاء الراوتر
$router = new App\Core\Router();

// تحميل جميع routes
require __DIR__ . '/../routes/web.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
