<?php

use Core\Controller;
use Models\User;

class UserController extends Controller
{
    public function index()
    {
        $this->view('register');
    }
    public function update()
    {
        var_dump('update method called');
        exit;
    }

    public function delete()
    {
        var_dump('delete method called');
        exit;
    }

    public function store()
    {
        try {

            $errors = [];

            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirm = trim($_POST['confirm_password'] ?? '');
            $role = trim($_POST['role'] ?? '');

            //Validation
            if (empty($username)) {
                $errors['username'] = 'Username is required';
            }

            if (empty($email)) {
                $errors['email'] = 'Email is required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Invalid email format';
            }

            if (empty($password)) {
                $errors['password'] = 'Password is required';
            } else if (strlen($password) < 6) {
                $errors['password'] = 'Password must be at least 6 characters';
            }

            if ($password !== $confirm) {
                $errors['confirm_password'] = 'Passwords do not match';
            }

            if (empty($role)) {
                $errors['role'] = 'Role is required';
            }

            $userModel = new User();

            if (empty($errors['email']) && $userModel->emailExists($email)) {
                $errors['email'] = 'Email already exists';
            }

            //return errors if any
            if (!empty($errors)) {
                $this->jsonResponse(false, 'Validation errors', ['errors' => $errors]);
                return;
            }

            //Hash password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            //Save to DB
            $result = $userModel->createUser($username, $email, $passwordHash, $role);

            if ($result) {
                $this->jsonResponse(true, 'User registered successfully');
            } else {
                $this->jsonResponse(false, 'Failed to register user');
            }
        } catch (Exception $e) {
            $this->jsonResponse(false, 'Server error: ' . $e->getMessage());
        }

    }
}