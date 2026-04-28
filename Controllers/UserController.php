<?php

use Core\Controller;
use Models\User;
use Traits\InputNormalizer;
use Validators\UserUpdateValidator;
use Validators\UserValidator;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    use InputNormalizer;
    public function index()
    {
        return $this->view('register');
    }

    // Handle user registration form submission, validate input, and return JSON response
    public function store(Request $request)
    {
        try {

            $errors = [];

            $inputs = $this->normalizeInputs(
                $request,
                [
                    'username',
                    'email',
                    'password',
                    'confirm_password',
                    'role'
                ]
            );

            $userModel = new User();

            //Validation
            $validator = new UserValidator($inputs, $userModel);
            $errors = $validator->validate();

            //return errors if any
            if (!empty($errors)) {
                echo $this->jsonResponse(false, 'Validation errors', ['errors' => $errors]);
                return;
            }

            //Hash password
            $passwordHash = password_hash($inputs['password'], PASSWORD_DEFAULT);

            //Save to DB
            $result = $userModel->createUser($inputs['username'], $inputs['email'], $passwordHash, $inputs['role']);

            if ($result) {
                echo $this->jsonResponse(true, 'User registered successfully');
            } else {
                echo $this->jsonResponse(false, 'Failed to register user');
            }
        } catch (Exception $e) {
            echo $this->jsonResponse(false, 'Server error: ' . $e->getMessage());
        }

    }

    // List all users, return an HTML view of users (id, username, email, role)
    public function list()
    {
        $userModel = new User();
        $users = $userModel->getAllUsers();

        return $this->view('users', ['users' => $users]);
    }

    // Show pre-filled edit form for user with given id
    public function edit(Request $request)
    {
        $id = $request->attributes->get('id');
        $userModel = new User();
        $user = $userModel->getUserById($id);

        if (!$user) {
            return $this->jsonResponse(false, 'User not found');
        }

        return $this->view('edit-user', ['user' => $user, 'errors' => [], 'old' => []]);
    }

    // Update user data
    public function update(Request $request)
    {
        try {
            $id = $request->attributes->get('id');

            // Get and normalize inputs
            $inputs = $this->normalizeInputs($request, ['username', 'email', 'role']);

            $userModel = new User();
            $user = $userModel->getUserById($id);

            if (!$user) {
                return $this->jsonResponse(false, 'User not found');
            }

            // Validate (password fields set to 'dummy' since edit doesn't have password)

            $validator = new UserUpdateValidator([
                'username' => $inputs['username'],
                'email' => $inputs['email'],
                'role' => $inputs['role']
            ], $userModel);
            $errors = $validator->validate();

            if (!empty($errors)) {
                return $this->jsonResponse(false, 'Validation errors', ['errors' => $errors]);
            }

            // Check if NEW email already exists for someone else (only if email was changed)
            $errors = [];

            if ($inputs['email'] !== $user['email'] && $userModel->emailExists($inputs['email'])) {
                $errors['email'] = 'Email already in use';
            }

            // Check if NEW username already exists for someone else (only if username was changed)
            if ($inputs['username'] !== $user['username'] && $userModel->usernameExists($inputs['username'])) {
                $errors['username'] = 'Username already in use';
            }

            if (!empty($errors)) {
                return $this->jsonResponse(false, 'Validation errors', ['errors' => $errors]);
            }

            // Update user
            $result = $userModel->updateUser($id, $inputs['username'], $inputs['email'], $inputs['role']);

            if ($result) {
                return $this->jsonResponse(true, 'User updated successfully');
            } else {
                return $this->jsonResponse(false, 'Failed to update user');
            }
        } catch (Exception $e) {
            return $this->jsonResponse(false, 'Server error: ' . $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $id = $request->attributes->get('id');
            $userModel = new User();
            $user = $userModel->getUserById($id);
            if (!$user) {
                return $this->jsonResponse(false, 'User not found');
            }
            $deleted = $userModel->softDelete($id);
            if ($deleted) {
                return $this->jsonResponse(true, 'User deleted');
            }
            return $this->jsonResponse(false, 'Failed to delete user');
        } catch (Exception $e) {
            return $this->jsonResponse(false, 'Server error: ' . $e->getMessage());
        }
    }
}
