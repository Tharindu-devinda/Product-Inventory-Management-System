<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

// Helper function to create routes
function addRoute($routes, $name, $path, $controller, $methods = ['GET'])
{
    $routes->add($name, new Route($path, [
        '_controller' => $controller
    ], [], [], '', [], $methods));
}

// Create routes using the helper function
addRoute($routes, 'register_view', '/users', 'UserController::index');
addRoute($routes, 'register_store', '/users/store', 'UserController::store', ['POST']);
addRoute($routes, 'register_update', '/users/update', 'UserController::update', ['POST']);
addRoute($routes, 'register_delete', '/users/delete', 'UserController::delete', ['POST']);

// Static views
$routes->add('login', new Route('/', ['file' => 'Views/login.php']));
$routes->add('dashboard', new Route('/dashboard', ['file' => 'Views/dashboard.php']));

return $routes;

