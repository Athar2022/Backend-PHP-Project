<?php

namespace App\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Helpers\Response;
use App\Core\Middleware;

class EmployeeController
{
    private $employeeModel;
    private $userModel;

    public function __construct()
    {
        $this->employeeModel = new Employee();
        $this->userModel = new User();
    }

    public function getAllEmployees()
    {
        Middleware::auth(["admin", "hr", "manager"]);
        $employees = $this->employeeModel->getAll();
        Response::success("Employees retrieved successfully.", $employees);
    }

    public function getEmployeeById(int $id)
    {
        Middleware::auth(["admin", "hr", "manager", "employee"]);
        $user = $_SERVER["user_data"];

        if ($user["role"] === "employee" && $user["id"] !== $id) {
            Response::error("Forbidden: You can only view your own profile.", 403);
        }

        $employee = $this->employeeModel->getEmployeeDetails($id);
        if ($employee) {
            Response::success("Employee retrieved successfully.", $employee);
        } else {
            Response::error("Employee not found.", 404);
        }
    }

    public function createEmployee()
    {
        Middleware::auth(["admin", "hr"]);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["name"]) || !isset($data["email"]) || !isset($data["password"]) || !isset($data["position"])) {
            Response::error("Missing required fields.", 400);
        }

        // Create user first
        $userData = [
            "name" => $data["name"],
            "email" => $data["email"],
            "password" => $data["password"],
            "role" => $data["role"] ?? "employee" // Default to employee role
        ];

        if ($this->userModel->findByEmail($userData["email"])) {
            Response::error("User with this email already exists.", 409);
        }

        if (!$this->userModel->create($userData)) {
            Response::error("Failed to create user.", 500);
        }

        $newUser = $this->userModel->findByEmail($userData["email"]);

        // Create employee
        $employeeData = [
            "user_id" => $newUser["id"],
            "name" => $data["name"],
            "position" => $data["position"],
            "phone" => $data["phone"] ?? null,
            "address" => $data["address"] ?? null,
            "hire_date" => $data["hire_date"] ?? date("Y-m-d"),
        ];

        if ($this->employeeModel->create($employeeData)) {
            Response::success("Employee created successfully.", [], 201);
        } else {
            Response::error("Failed to create employee.", 500);
        }
    }

    public function updateEmployee(int $id)
    {
        Middleware::auth(["admin", "hr"]);
        $data = json_decode(file_get_contents("php://input"), true);

        if ($this->employeeModel->update($id, $data)) {
            Response::success("Employee updated successfully.");
        } else {
            Response::error("Failed to update employee or employee not found.", 500);
        }
    }

    public function deleteEmployee(int $id)
    {
        Middleware::auth(["admin", "hr"]);
        if ($this->employeeModel->delete($id)) {
            Response::success("Employee deleted successfully.");
        } else {
            Response::error("Failed to delete employee or employee not found.", 500);
        }
    }
}


