<?php

namespace App\Models;

use App\Core\Model;

class Leave extends Model
{
    protected $table = 'leaves';

    public function __construct()
    {
        parent::__construct();
    }

    public function getEmployeeLeaves(int $employeeId)
    {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE employee_id = :employee_id");
        $stmt->bindParam(":employee_id", $employeeId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}


