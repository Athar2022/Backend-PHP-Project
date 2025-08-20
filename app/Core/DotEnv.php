<?php

namespace App\Core;

class DotEnv
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function load()
    {
        if (!file_exists($this->path)) {
            throw new \Exception(".env file not found: " . $this->path);
        }
        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            list($name, $value) = array_map('trim', explode('=', $line, 2));
            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
                putenv("{$name}={$value}");
            }
        }
    }

    public static function env($key, $default = null)
    {
        return $_ENV[$key] ?? getenv($key) ?? $default;
    }
}
