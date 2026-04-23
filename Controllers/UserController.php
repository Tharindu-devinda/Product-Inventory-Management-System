<?php

use Core\Controller;
use Models\User;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function index()
    {
       return $this->view('register');
    }

    public function store(Request $request)
    {
        try {

            $errors = [];

            $username = trim($request->request->get('username') ?? '');
            $email = trim($request->request->get('email') ?? '');
            $password = trim($request->request->get('password') ?? '');
            $confirm = trim($request->request->get('confirm_password') ?? '');
            $role = trim($request->request->get('role') ?? '');

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
                echo $this->jsonResponse(false, 'Validation errors', ['errors' => $errors]);
                return;
            }

            //Hash password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            //Save to DB
            $result = $userModel->createUser($username, $email, $passwordHash, $role);

            if ($result) {
                echo $this->jsonResponse(true, 'User registered successfully');
            } else {
                $this->jsonResponse(false, 'Failed to register user');
            }
        } catch (Exception $e) {
            $this->jsonResponse(false, 'Server error: ' . $e->getMessage());
        }

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
}