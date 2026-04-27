<?php

namespace Models;

use Config\DbConnector;

class User
{

    private $conn;

    public function __construct()
    {
        $db = new DbConnector();
        $this->conn = $db->connect();
    }

    public function createUser($username, $email, $passwordHash, $role)
    {

        $sql = "INSERT INTO users (username, email, password, role)
                VALUES (:username, :email, :password, :role)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $passwordHash,
            ':role' => $role
        ]);
    }

    public function emailExists($email)
    {

        $sql = "SELECT id FROM users WHERE email = :email AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function usernameExists($username)
    {
        $sql = "SELECT id FROM users WHERE username = :username AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':username' => $username]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}