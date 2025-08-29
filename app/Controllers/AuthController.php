<?php

namespace App\Controllers;

use App\Models\User;
use App\Helpers\JWTHandler;
use App\Helpers\Response;

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["name"]) || !isset($data["email"]) || !isset($data["password"])) {
            Response::error("Please provide name, email, and password.", 400);
        }

        $data["role"] = isset($data["role"]) && in_array($data["role"], ["admin", "hr", "manager", "employee"]) ? $data["role"] : "employee";

        if ($this->userModel->findByEmail($data["email"])) {
            Response::error("User with this email already exists.", 409);
        }

        if ($this->userModel->create($data)) {
            Response::success("User registered successfully.", [], 201);
        } else {
            Response::error("Failed to register user.", 500);
        }
    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["email"]) || !isset($data["password"])) {
            Response::error("Please provide email and password.", 400);
        }

        $user = $this->userModel->findByEmail($data["email"]);

        if (!$user || !password_verify($data["password"], $user["password"])) {
            Response::error("Invalid credentials.", 401);
        }

        $tokenData = [
            "id" => $user["id"],
            "name" => $user["name"],
            "email" => $user["email"],
            "role" => $user["role"]
        ];
        $token = JWTHandler::generateToken($tokenData);

        Response::success("Login successful.", [
            "token" => $token,
            "user" => [
                "id" => $user["id"],
                "name" => $user["name"],
                "email" => $user["email"],
                "role" => $user["role"]
            ]
        ]);
    }
}


