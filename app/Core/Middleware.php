<?php

namespace App\Core;

use App\Helpers\JWTHandler;
use App\Helpers\Response;

class Middleware
{
    public static function auth(array $allowedRoles = [])
    {
        $headers = getallheaders();
        $authHeader = $headers["Authorization"] ?? null;

        if (!$authHeader) {
            Response::error("Authorization token not provided.", 401);
        }

        list($bearer, $token) = explode(" ", $authHeader);

        if ($bearer !== "Bearer" || !$token) {
            Response::error("Invalid Authorization header format.", 401);
        }

        $decodedToken = JWTHandler::decodeToken($token);

        if (!$decodedToken) {
            Response::error("Invalid or expired token.", 401);
        }

   
        if (!empty($allowedRoles) && !in_array($decodedToken["role"], $allowedRoles)) {
            Response::error("Forbidden: You do not have permission to access this resource.", 403);
        }

        $_SERVER["user_data"] = $decodedToken;
    }
}


