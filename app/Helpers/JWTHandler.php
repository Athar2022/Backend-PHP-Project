<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Core\App;

class JWTHandler
{
    private static $secretKey;
    private static $expirationTime;

    public static function init()
    {
        self::$secretKey = App::env('JWT_SECRET');
        self::$expirationTime = App::env('JWT_EXPIRATION', 3600); // Default to 1 hour
    }

    public static function generateToken(array $data): string
    {
        self::init();
        $issuedAt = time();
        $expirationTime = $issuedAt + self::$expirationTime;

        $payload = [
            'iat' => $issuedAt, 
            'exp' => $expirationTime, 
            'data' => $data 
        ];

        return JWT::encode($payload, self::$secretKey, 'HS256');
    }

    public static function decodeToken(string $token)
    {
        self::init();
        try {
            $decoded = JWT::decode($token, new Key(self::$secretKey, 'HS256'));
            return (array) $decoded->data;
        } catch (\Exception $e) {
            return false; 
        }
    }
}


