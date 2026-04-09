<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

$routes->add('register', new Route('/register', [
    'file' => 'Views/register.php'
]));

$routes->add('login', new Route('/', [
    'file' => 'Views/login.php'
]));

$routes->add('dashboard', new Route('/dashboard', [
    'file' => 'Views/dashboard.php'
]));



return $routes;

