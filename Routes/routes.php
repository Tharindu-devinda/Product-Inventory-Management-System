<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

$routes->add('register_view', new Route('/create_user', [
    '_controller' => 'UserController::index'
]));

$routes->add('register_store', new Route('/register/store_user', [
    '_controller' => 'UserController::store',
    'methods' => ['POST']
]));

$routes->add('register_update', new Route('/register/update_user', [
    '_controller' => 'UserController::update',
    'methods' => ['POST']
]));

$routes->add('register_delete', new Route('/register/delete_user', [
    '_controller' => 'UserController::delete',
    'methods' => ['POST']
]));

$routes->add('login', new Route('/', [
    'file' => 'Views/login.php'
]));

$routes->add('dashboard', new Route('/dashboard', [
    'file' => 'Views/dashboard.php'
]));



return $routes;

