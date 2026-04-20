<?php
require_once __DIR__ . '/../Config/db.php';
class User
{

    private $conn;

    public function __construct()
    {
        $db = new DbConnector();
        $this->conn = $db->connect();
    }

    public function createUser($username, $email, $passwordHash)
    {

        $sql = "INSERT INTO users (username, email, password)
                VALUES (:username, :email, :password)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $passwordHash
        ]);
    }

    public function emailExists($email)
    {

        $sql = "SELECT id FROM users WHERE email = :email AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}