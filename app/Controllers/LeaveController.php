<?php

namespace App\Controllers;

use App\Models\Leave;
use App\Helpers\Response;
use App\Core\Middleware;

class LeaveController
{
    private $leaveModel;

    public function __construct()
    {
        $this->leaveModel = new Leave();
    }

    public function getAllLeaves()
    {
        Middleware::auth(["admin", "hr", "manager"]);
        $leaves = $this->leaveModel->getAll();
        Response::success("Leaves retrieved successfully.", $leaves);
    }

    public function getLeaveById(int $id)
    {
        Middleware::auth(["admin", "hr", "manager", "employee"]);
        $user = $_SERVER["user_data"];

        $leave = $this->leaveModel->getById($id);

        if (!$leave) {
            Response::error("Leave request not found.", 404);
        }

        // Employee can only view their own leave requests
        if ($user["role"] === "employee" && $leave["employee_id"] !== $user["id"]) {
            Response::error("Forbidden: You can only view your own leave requests.", 403);
        }

        Response::success("Leave request retrieved successfully.", $leave);
    }

    public function createLeave()
    {
        Middleware::auth(["employee"]); 
        $data = json_decode(file_get_contents("php://input"), true);
        $user = $_SERVER["user_data"];

        if (!isset($data["start_date"]) || !isset($data["end_date"]) || !isset($data["reason"])) {
            Response::error("Missing required fields: start_date, end_date, reason.", 400);
        }

        $data["employee_id"] = $user["id"]; 
        $data["status"] = "pending"; 

        if ($this->leaveModel->create($data)) {
            Response::success("Leave request created successfully.", [], 201);
        } else {
            Response::error("Failed to create leave request.", 500);
        }
    }

    public function updateLeaveStatus(int $id)
    {
        Middleware::auth(["admin", "hr", "manager"]); 
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["status"]) || !in_array($data["status"], ["approved", "rejected", "pending"])) {
            Response::error("Invalid status provided. Must be approved, rejected, or pending.", 400);
        }

        if ($this->leaveModel->update($id, ["status" => $data["status"]])) {
            Response::success("Leave status updated successfully.");
        } else {
            Response::error("Failed to update leave status or leave request not found.", 500);
        }
    }

    public function deleteLeave(int $id)
    {
        Middleware::auth(["admin", "hr"]); 
        if ($this->leaveModel->delete($id)) {
            Response::success("Leave request deleted successfully.");
        } else {
            Response::error("Failed to delete leave request or leave request not found.", 500);
        }
    }

    
}


