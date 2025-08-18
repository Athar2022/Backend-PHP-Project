<?php

namespace App\Core;

use PDO;
use PDOException;
final class App
{
    private static ?PDO $pdo = null;
    private static array $config = [];
    public static function init(): void
    {
        // Load .env
        self::$config = self::loadEnv();
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        try {
            $dsn = "mysql:host=" . self::env("DB_HOST") . ";dbname=" . self::env("DB_NAME") . ";charset=utf8mb4";
            self::$pdo = new PDO(
                $dsn,
                self::env("DB_USER"),
                self::env("DB_PASS"),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function db(): PDO
    {
        if (self::$pdo === null) {
            self::init();
        }
        return self::$pdo;
    }

    private static function loadEnv(): array
    {
        $envPath = __DIR__ . '/../../.env';
        $vars = [];
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES |
                FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                [$key, $value] = explode("=", $line, 2);

                $vars[trim($key)] = trim($value);

            }
        }
        return $vars;
    }
    public static function env(string $key, $default = null)
    {
        return self::$config[$key] ?? $default;
    }
}

