<?php

use Core\Controller;
use Models\User;
use Validators\UserValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

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

            $username = $this->normalizeInput($request->request->get('username'));
            $email = $this->normalizeInput($request->request->get('email'));
            $password = $this->normalizeInput($request->request->get('password'));
            $confirm = $this->normalizeInput($request->request->get('confirm_password'));
            $role = $this->normalizeInput($request->request->get('role'));

            $userModel = new User();

            //Validation
            $validator = new UserValidator([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'confirm_password' => $confirm,
                'role' => $role
            ], $userModel);
            $errors = $validator->validate();

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
                echo $this->jsonResponse(false, 'Failed to register user');
            }
        } catch (Exception $e) {
            echo $this->jsonResponse(false, 'Server error: ' . $e->getMessage());
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

// Helper function to create routes
function addRoute($routes, $name, $path, $controller, $methods = ['GET'])
{
    $routes->add($name, new Route($path, [
        '_controller' => $controller
    ], [], [], '', [], $methods));
}

// Add routes
addRoute($routes, 'register_view', '/users', 'UserController::index');
addRoute($routes, 'register_store', '/users/store', 'UserController::store', ['POST']);
addRoute($routes, 'register_update', '/users/update', 'UserController::update', ['POST']);
addRoute($routes, 'register_delete', '/users/delete', 'UserController::delete', ['POST']);

// Static views (can be handled differently)
$routes->add('login', new Route('/', ['file' => 'Views/login.php']));
$routes->add('dashboard', new Route('/dashboard', ['file' => 'Views/dashboard.php']));

return $routes;