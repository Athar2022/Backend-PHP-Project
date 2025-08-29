<?php

namespace App\Controllers;

use App\Models\Salary;
use App\Helpers\Response;
use App\Core\Middleware;

class SalaryController
{
    private $salaryModel;

    public function __construct()
    {
        $this->salaryModel = new Salary();
    }

    public function getAllSalaries()
    {
        Middleware::auth(["admin", "hr"]);
        $salaries = $this->salaryModel->getAll();
        Response::success("Salaries retrieved successfully.", $salaries);
    }

    public function getEmployeeSalary(int $employeeId)
    {
        Middleware::auth(["admin", "hr", "manager", "employee"]);
        $user = $_SERVER["user_data"];

        // Employee can only view their own salary
        if ($user["role"] === "employee" && $employeeId !== $user["id"]) {
            Response::error("Forbidden: You can only view your own salary.", 403);
        }

        $salary = $this->salaryModel->getEmployeeSalary($employeeId);
        if ($salary) {
            Response::success("Employee salary retrieved successfully.", $salary);
        } else {
            Response::error("Salary not found for this employee.", 404);
        }
    }

    public function createSalary()
    {
        Middleware::auth(["admin", "hr"]);
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["employee_id"]) || !isset($data["amount"]) || !isset($data["payment_date"])) {
            Response::error("Missing required fields: employee_id, amount, payment_date.", 400);
        }

        if ($this->salaryModel->create($data)) {
            Response::success("Salary created successfully.", [], 201);
        } else {
            Response::error("Failed to create salary.", 500);
        }
    }

    public function updateSalary(int $id)
    {
        Middleware::auth(["admin", "hr"]);
        $data = json_decode(file_get_contents("php://input"), true);

        if ($this->salaryModel->update($id, $data)) {
            Response::success("Salary updated successfully.");
        } else {
            Response::error("Failed to update salary or salary not found.", 500);
        }
    }

    public function deleteSalary(int $id)
    {
        Middleware::auth(["admin", "hr"]);
        if ($this->salaryModel->delete($id)) {
            Response::success("Salary deleted successfully.");
        } else {
            Response::error("Failed to delete salary or salary not found.", 500);
        }
    }
}


