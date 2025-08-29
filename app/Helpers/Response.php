<?php

namespace App\Helpers;

class Response
{
    public static function json($data, int $statusCode = 200)
    {
        header("Content-Type: application/json");
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }

    public static function error(string $message, int $statusCode = 400)
    {
        self::json(["message" => $message], $statusCode);
    }

    public static function success(string $message, $data = [], int $statusCode = 200)
    {
        self::json(["message" => $message, "data" => $data], $statusCode);
    }
}


