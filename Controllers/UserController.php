<?php

require_once __DIR__ . '/../Models/User.php';

class UserController
{
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $confirmPassword = trim($_POST['confirm_password']);

            // Validation
            if (empty($username) || empty($email) || empty($password)) {
                echo "<script>
                        alert('All fields are required');
                        window.location.href = '/register';
                      </script>";
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "<script>
                        alert('Invalid email format');
                        window.location.href = '/register';
                      </script>";
                return;
            }

            if (strlen($password) < 6) {
                echo "<script>
                        alert('Password must be at least 6 characters');
                        window.location.href = '/register';
                      </script>";
                return;
            }

            // Check password match
            if ($password !== $confirmPassword) {
                echo "<script>
                        alert('Passwords do not match');
                        window.location.href = '/register';
                    </script>";
                return;
            }

            $userModel = new User();

            if ($userModel->emailExists($email)) {
                echo "<script>
                        alert('Email already exists');
                        window.location.href = '/register';
                      </script>";
                return;
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $result = $userModel->createUser($username, $email, $passwordHash);

            if ($result) {
                echo "<script>
                        alert('User registered successfully');
                        window.location.href = '/register';
                      </script>";
            } else {
                echo "<script>
                        alert('Registration failed');
                        window.location.href = '/register';
                      </script>";
            }

        } else {
            require __DIR__ . '/../Views/register.php';
        }
    }
}