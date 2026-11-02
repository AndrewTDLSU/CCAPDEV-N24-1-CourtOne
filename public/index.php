<?php
/**
 * Application Front Controller Router System
 * Acts as the centralized entrance gateway routing all requests to their target components
 */
session_start();

// 1. Dependency Auto-loader
require_once '../core/Database.php';
require_once '../core/Controller.php';
require_once '../core/Model.php';

// 2. Simple fallback routing system parser (Splits parameters from domain paths: index.php?url=controller/method)
$url = $_GET['url'] ?? 'home/index';
$urlParts = explode('/', filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL));

$controllerName = ucfirst($urlParts[0]) . 'Controller';
$methodName = $urlParts[1] ?? 'index';
$params = array_slice($urlParts, 2);

$controllerFile = '../controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $methodName)) {
            call_user_func_array([$controller, $methodName], $params);
            exit;
        }
    }
}

header("HTTP/1.0 404 Not Found");
echo "<h1>404 Error: Destination controller or method mapping path is broken.</h1>";
