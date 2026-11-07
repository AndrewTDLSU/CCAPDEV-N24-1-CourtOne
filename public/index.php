<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Model.php';

// 3. Parse incoming URL parameters
$url = $_GET['url'] ?? 'home/index';
$urlParts = explode('/', filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL));

$controllerInput = !empty($urlParts[0])
    ? strtolower($urlParts[0])
    : 'home';

$controllerName = '';

if ($controllerInput === 'home') {
    $controllerName = 'HomeController';
} elseif ($controllerInput === 'auth') {
    $controllerName = 'AuthController';
} elseif ($controllerInput === 'dashboard') {
    $controllerName = 'DashboardController';
} elseif ($controllerInput === 'venue') {
    $controllerName = 'VenueController';
} elseif ($controllerInput === 'booking') {
    $controllerName = 'BookingController';
} else {
    $controllerName = ucfirst($controllerInput) . 'Controller';
}

$methodName = isset($urlParts[1]) && !empty($urlParts[1])
    ? $urlParts[1]
    : 'index';

$params = array_slice($urlParts, 2);

$controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;

    if (class_exists($controllerName)) {
        $controllerObject = new $controllerName();

        if (method_exists($controllerObject, $methodName)
            && is_callable([$controllerObject, $methodName])
            && !str_starts_with($methodName, '__')) {
            call_user_func_array([$controllerObject, $methodName], $params);
            exit;
        }
    }
}

require_once __DIR__ . '/../controllers/HomeController.php';

$fallback = new HomeController();
$fallback->index();
?>
