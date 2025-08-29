<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\EmployeeController;
use App\Controllers\LeaveController;
use App\Controllers\SalaryController;

$router = new Router();

// Authentication Routes
$router->post("/api/v1/register", [AuthController::class, "register"]);
$router->post("/api/v1/login", [AuthController::class, "login"]);

// Employee Routes
$router->get("/http://localhost/Backend-PHP-Project/public/api/v1/employees", [EmployeeController::class, "getAllEmployees"]);
$router->get("/api/v1/employees/{id}", [EmployeeController::class, "getEmployeeById"]);
$router->post("/api/v1/employees", [EmployeeController::class, "createEmployee"]);
$router->put("/api/v1/employees/{id}", [EmployeeController::class, "updateEmployee"]);
$router->delete("/api/v1/employees/{id}", [EmployeeController::class, "deleteEmployee"]);

// Leave Routes
$router->get("/api/v1/leaves", [LeaveController::class, "getAllLeaves"]);
$router->get("/api/v1/leaves/{id}", [LeaveController::class, "getLeaveById"]);
$router->post("/api/v1/leaves", [LeaveController::class, "createLeave"]);
$router->put("/api/v1/leaves/{id}/status", [LeaveController::class, "updateLeaveStatus"]);
$router->delete("/api/v1/leaves/{id}", [LeaveController::class, "deleteLeave"]);

// Salary Routes
$router->get("/api/v1/salaries", [SalaryController::class, "getAllSalaries"]);
$router->get("/api/v1/employees/{id}/salary", [SalaryController::class, "getEmployeeSalary"]);
$router->post("/api/v1/salaries", [SalaryController::class, "createSalary"]);
$router->put("/api/v1/salaries/{id}", [SalaryController::class, "updateSalary"]);
$router->delete("/api/v1/salaries/{id}", [SalaryController::class, "deleteSalary"]);
