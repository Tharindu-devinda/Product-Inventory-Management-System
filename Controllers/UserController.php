<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Core/Controller.php';

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
        $errors = [];
        $old = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $confirm = trim($_POST['confirm_password']);

            $old = [
                'username' => $username,
                'email' => $email
            ];

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

            $userModel = new User();

            if ($userModel->emailExists($email)) {
                $errors['email'] = 'Email already exists';
            }

            if (!empty($errors)) {
                return $this->view('register', [
                    'errors' => $errors,
                    'old' => $old
                ]);
            }

            //Hash password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            //Save to DB
            $result = $userModel->createUser($username, $email, $passwordHash);
            if ($result) {
                return $this->view('register', [
                    'success' => 'User registered successfully!'
                ]);
            }
        }
    }
}