<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

$routes->add('register_view', new Route('/users', [
    '_controller' => 'UserController::index'
]));

$routes->add('register_store', new Route('/users/store', [
    '_controller' => 'UserController::store',
], [], [], '', [], ['POST']));

$routes->add('register_update', new Route('/users/update', [
    '_controller' => 'UserController::update',
], [], [], '', [], ['POST']));

$routes->add('register_delete', new Route('/users/delete', [
    '_controller' => 'UserController::delete',
], [], [], '', [], ['POST']));

$routes->add('login', new Route('/', [
    'file' => 'Views/login.php'
]));

$routes->add('dashboard', new Route('/dashboard', [
    'file' => 'Views/dashboard.php'
]));



return $routes;

