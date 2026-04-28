<?php
namespace Helpers;

use Symfony\Component\Routing\Route;

class RouteHelper
{
    public static function addRoute($routes, $name, $path, $controller, $methods = ['GET'])
    {
        $routes->add($name, new Route(path: $path, defaults: [
            '_controller' => $controller
        ], methods: $methods));
    }
}