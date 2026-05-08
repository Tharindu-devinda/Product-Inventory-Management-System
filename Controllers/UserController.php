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
        return $this->view('users', ['users' => (new User())->getAllUsers()]);
    }

    // Show pre-filled edit form for user with given id
    // return HTML view or JSON error if user not found
    public function edit(Request $request)
    {
        $id = $request->attributes->get('id');
        $userModel = new User();
        $user = $userModel->getUserById($id);

        if (!$user) {
            return $this->jsonResponse(false, 'User not found');
        }

        return $this->view('edit-user', ['user' => $user]);
    }

    // Update user data, validate input and return JSON response
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

            // Validate input (includes format validation and duplicate checks)
            $validator = new UserUpdateValidator($inputs, $userModel, $user);
            $errors = $validator->validate();

            if (!empty($errors)) {
                return $this->jsonResponse(false, 'Validation errors', ['errors' => $errors]);
            }

            // Update user
            if ($userModel->updateUser($id, $inputs['username'], $inputs['email'], $inputs['role'])) {
                return $this->jsonResponse(true, 'User updated successfully');
            }

            return $this->jsonResponse(false, 'Failed to update user');
        } catch (Exception $e) {
            return $this->jsonResponse(false, 'Server error: ' . $e->getMessage());
        }
    }

    // Soft delete user by id
    // return JSON response indicating success or failure
    public function delete(Request $request)
    {
        try {
            $id = $request->attributes->get('id');

            if ((new User())->softDelete($id)) {
                return $this->jsonResponse(true, 'User deleted successfully');
            } else {
                return $this->jsonResponse(false, 'User not found or already deleted');
            }

        } catch (Exception $e) {
            return $this->jsonResponse(false, 'Server error: ' . $e->getMessage());
        }
    }

    public function login(): string
    {
        return $this->view('login', ['hideNav' => true]);
    }

    // Handle user login, validate credentials, and start session
    public function authenticate(Request $request): string
    {
        try {
            $inputs = $this->normalizeInputs($request, ['email', 'password']);

            $userModel = new User();
            $user = $userModel->getUserByEmail($inputs['email']);

            if (!$user) {
                return $this->jsonResponse(false, 'Invalid email or password');
            }

            if (!password_verify($inputs['password'], $user['password'])) {
                return $this->jsonResponse(false, 'Invalid email or password');
            }

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
        
            return $this->jsonResponse(true, 'Login successful', ['redirect' => '/dashboard']);

        } catch (Exception $e) {
            return $this->jsonResponse(false, 'Server error: ' . $e->getMessage());
        }
    }

    // Handle user logout - destroy session and redirect to login
    public function logout(Request $request): string
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Destroy all session data
        session_destroy();

        // Redirect to login page
        header('Location: /login');
        exit;
    }
}
