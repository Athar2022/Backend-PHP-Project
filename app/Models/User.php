<?php

namespace App\Models;
use App\Core\App;

class User
{
    function search($query)
    {
        $stm = App::db()->prepare("SELECT * FROM users WHERE email LIKE :q");
        $stm->execute(['q' => "%$query%"]);
        return $stm->fetchAll();
    }

    function all()
    {
        $stm = App::db()->prepare("SELECT * FROM users");

        $stm->execute();

        return $stm->fetchAll();
    }

    function find($id)
    {
        $stm = App::db()->prepare("SELECT * FROM users WHERE id=:id");
        $stm->execute(['id' => $id]);
        return $stm->fetch();
    }

    function create($email, $password)
    {
        $stm = App::db()->prepare("INSERT INTO users(email, password) VALUES (:email, :password)");
        $stm->execute(['email' => $email, 'password' => $password]);
    }
    function findByEmail($email)
    {
        $stm = App::db()->prepare("SELECT * FROM users WHERE email=:email");
        $stm->execute(['email' => $email]);
        return $stm->fetch();
    }

    function update($id, $email, $password = null)
    {
        if ($password) {
            $stm = App::db()->prepare("UPDATE users SET email=:email, password=:password WHERE id = :id");
            $stm->execute(['id' => $id, 'email' => $email, 'password' => $password]);
        } else {
            $stm = App::db()->prepare("UPDATE users SET email=:email WHERE id = :id");
            $stm->execute(['id' => $id, 'email' => $email]);
        }
    }

    function delete($id)
    {
        $stm = App::db()->prepare("DELETE FROM users WHERE id=:id");
        $stm->execute(['id' => $id]);
    }
}
