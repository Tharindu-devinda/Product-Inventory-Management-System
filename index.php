<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Config/db.php';

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;

$routes = require __DIR__ . '/Routes/routes.php';

//Get information about the current request
$request = Request::createFromGlobals();

//Get the base path
$scriptName = $request->server->get('SCRIPT_NAME');
$basePath = dirname($scriptName);

// Get the full request path and remove the base path
$requestPath = $request->getPathInfo();
if (strpos($requestPath, $basePath) === 0) {
    $pathInfo = substr($requestPath, strlen($basePath));
} else {
    $pathInfo = $requestPath;
}
$pathInfo = '/' . ltrim($pathInfo, '/');

// Create the routing context
$context = new RequestContext();
$context->fromRequest($request);

//Create a matcher that can find which route matches the URL
$matcher = new UrlMatcher($routes, $context);

try {
    //Find which route matches the current URL
    $params = $matcher->match($pathInfo);

    if (isset($params['_controller'])) {

        // Example: UserController::register
        list($controllerName, $methodName) = explode('::', $params['_controller']);

        // Build full class name (adjust namespace if you use one)
        $controllerFile = __DIR__ . '/Controllers/' . $controllerName . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;

            $controller = new $controllerName();

            if (method_exists($controller, $methodName)) {
                $output = $controller->$methodName($request);

                if ($output !== null) {
                    // Check if it's JSON
                    if (json_decode($output, true) !== null) {
                        header('Content-Type: application/json');
                    } else {
                        header('Content-Type: text/html; charset=UTF-8');
                    }
                    echo $output;
                }
            } else {
                echo "Method not found";
            }
        } else {
            echo "Controller not found";
        }

    } else {
        echo "No controller defined for this route";
    }

} catch (Exception $e) {
    // No route matched - show 404 error
    http_response_code(404);
    echo "404 - Page not found";
}
?>