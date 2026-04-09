<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;

//Load routes
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

    // Get the file path from the matched route
    $file = __DIR__ . '/' . $params['file'];

    //Check if the file exists and include it
    if (file_exists($file)) {
        include $file; 
    } else {
        echo "404 - File not found: " . $file;
    }

} catch (Exception $e) {
    // No route matched - show 404 error
    http_response_code(404);
    echo "404 - Page not found";
}
?>