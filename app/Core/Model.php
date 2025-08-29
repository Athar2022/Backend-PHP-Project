<?php

namespace App\Core;

use PDO;
use App\Core\App;

abstract class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = App::db();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM " . $this->table);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $query = "INSERT INTO " . $this->table . " ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($query);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        return $stmt->execute();
    }

    public function update(int $id, array $data)
    {
        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "{$key} = :{$key}";
        }
        $setClause = implode(", ", $setClause);
        $query = "UPDATE " . $this->table . " SET {$setClause} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete(int $id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
