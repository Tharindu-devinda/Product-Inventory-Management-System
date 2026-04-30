<?php

use Symfony\Component\Routing\RouteCollection;
use Helpers\RouteHelper;

$routes = new RouteCollection();

// Create routes using the helper function
// User routes
RouteHelper::addRoute($routes, 'register_view', '/users', 'UserController::index');
RouteHelper::addRoute($routes, 'users_view', '/users-list', 'UserController::list');
RouteHelper::addRoute($routes, 'register_store', '/users/store', 'UserController::store', ['POST']);
RouteHelper::addRoute($routes, 'register_update', '/users/update', 'UserController::update', ['POST']);
RouteHelper::addRoute($routes, 'register_delete', '/users/delete', 'UserController::delete', ['POST']);

// Product routes
RouteHelper::addRoute($routes, 'product_create_view', '/products/create', 'ProductController::index');
RouteHelper::addRoute($routes, 'product_store', '/products/store', 'ProductController::store', ['POST']);
RouteHelper::addRoute($routes, 'product_list', '/products', 'ProductController::list');
RouteHelper::addRoute($routes, 'product_edit', '/products/{id}/edit', 'ProductController::edit');
RouteHelper::addRoute($routes, 'product_update', '/products/{id}/update', 'ProductController::update', ['POST']);
RouteHelper::addRoute($routes, 'product_delete', '/products/{id}/delete', 'ProductController::delete', ['POST']);

RouteHelper::addRoute($routes, 'dashboard', '/dashboard', 'DashboardController::index');

return $routes;