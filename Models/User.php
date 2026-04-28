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

    // Create a new user, return true on success, false on failure
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

    // Fetch all users (excluding deleted ones), 
    // return an array of users with id, username, email, and role
    public function getAllUsers()
    {
        $sql = "SELECT id, username, email, role FROM users WHERE deleted_at IS NULL";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Check if email already exists, return true if exists, false otherwise
    public function emailExists($email)
    {

        $sql = "SELECT id FROM users WHERE email = :email AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);

        return $stmt->fetch(\PDO::FETCH_ASSOC) !== false;
    }
    // Check if username already exists, return true if exists, false otherwise  
    public function usernameExists($username)
    {
        $sql = "SELECT id FROM users WHERE username = :username AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':username' => $username]);

        return $stmt->fetch(\PDO::FETCH_ASSOC) !== false;
    }

    // Check if email exists for ANOTHER user (exclude current user by ID)
    public function emailExistsExcept($email, $id)
    {
        $sql = "SELECT id FROM users WHERE email = :email AND id != :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email, ':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) !== false;
    }

    // Check if username exists for ANOTHER user (exclude current user by ID)
    public function usernameExistsExcept($username, $id)
    {
        $sql = "SELECT id FROM users WHERE username = :username AND id != :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':username' => $username, ':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) !== false;
    }

    //get single user by id, return user data or false if not found
    public function getUserById($id)
    {
        $stmt = $this->conn->prepare("SELECT id, username, email, role FROM users WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    //update user by id, return true on success, false on failure
    public function updateUser($id, $username, $email, $role)
    {
        $sql = "UPDATE users 
            SET username = :username, email = :email, role = :role 
            WHERE id = :id AND deleted_at IS NULL";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':role' => $role,
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0; // returns true if a row was updated
    }

    // checks if email or username exists for ANOTHER user
    public function existsExceptId($email, $username, $id)
    {
        return $this->emailExistsExcept($email, $id) || $this->usernameExistsExcept($username, $id);
    }

    // soft-delete user by id (sets deleted_at)
    public function softDelete($id)
    {
        $sql = "UPDATE users SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}