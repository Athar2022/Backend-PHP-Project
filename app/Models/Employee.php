<?php

namespace App\Models;

use PDO;
use PDOException;

use App\Core\Model;

class Employee extends Model
{
    protected $table = 'employees';

    public function __construct()
    {
        parent::__construct();
    }

     public function getByEmail(string $email)
    {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findByUserId(int $userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getEmployeeDetails(int $id)
    {
        $query = "SELECT e.*, u.email, u.role, s.amount as salary_amount, s.final_salary, s.deductions, s.bonuses, s.payment_date, c.start_date as contract_start, c.end_date as contract_end, c.type as contract_type
                  FROM employees e
                  LEFT JOIN users u ON e.user_id = u.id
                  LEFT JOIN salaries s ON e.id = s.employee_id
                  LEFT JOIN contracts c ON e.id = c.employee_id
                  WHERE e.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}


