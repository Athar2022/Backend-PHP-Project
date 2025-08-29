<?php
namespace App\Controllers;

use App\Models\Employee;
use App\Helpers\Response;

class LoginController
{
    private $employeeModel;

    public function __construct()
    {
        $this->employeeModel = new Employee();
    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['email']) || !isset($data['password'])) {
            Response::error("Email and password required", 400);
        }

        $user = $this->employeeModel->getByEmail($data['email']);
        if (!$user || !password_verify($data['password'], $user['password'])) {
            Response::error("Invalid email or password", 401);
        }

        session_start();
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'role' => $user['role']
        ];

        Response::success("Login successful", $_SESSION['user']);
    }
}
