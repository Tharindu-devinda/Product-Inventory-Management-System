<?php

use Symfony\Component\Routing\RouteCollection;
use Helpers\RouteHelper;

$routes = new RouteCollection();

// Create routes using the helper function
RouteHelper::addRoute($routes, 'register_view', '/users', 'UserController::index');
RouteHelper::addRoute($routes, 'users_view', '/users-list', 'UserController::list');
RouteHelper::addRoute($routes, 'register_store', '/users/store', 'UserController::store', ['POST']);
RouteHelper::addRoute($routes, 'register_update', '/users/update', 'UserController::update', ['POST']);
RouteHelper::addRoute($routes, 'register_delete', '/users/delete', 'UserController::delete', ['POST']);

RouteHelper::addRoute($routes, 'dashboard', '/dashboard', 'DashboardController::index');

return $routes;