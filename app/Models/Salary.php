<?php

namespace App\Models;

use App\Core\Model;

class Salary extends Model
{
    protected $table = 'salaries';

    public function __construct()
    {
        parent::__construct();
    }

    public function getEmployeeSalary(int $employeeId)
    {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE employee_id = :employee_id");
        $stmt->bindParam(":employee_id", $employeeId);
        $stmt->execute();
        return $stmt->fetch();
    }
}


