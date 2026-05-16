<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/Auth.php';

$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

Auth::enforceFirstLogin($page, $action);

$routes = [
    'dashboard' => 'DashboardController',
    'home' => 'DashboardController',
    'auth' => 'AuthController',
    'specializations' => 'SpecializationController',
    'doctors' => 'DoctorController',
    'users' => 'UserController',
    'appointments' => 'AppointmentController',
    'prescriptions' => 'PrescriptionController',
    'reports' => 'ReportController',
];

if ($page === 'home') {
    $page = 'dashboard';
}

if ($page === 'error') {
    if ($action === '403') {
        require __DIR__ . '/views/errors/403.php';
    } else {
        require __DIR__ . '/views/errors/404.php';
    }
    exit;
}

if (!isset($routes[$page])) {
    http_response_code(404);
    require __DIR__ . '/views/errors/404.php';
    exit;
}

$controllerFile = __DIR__ . '/controllers/' . $routes[$page] . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(404);
    require __DIR__ . '/views/errors/404.php';
    exit;
}

require_once $controllerFile;
$controllerClass = $routes[$page];
$controller = new $controllerClass();

if (!method_exists($controller, $action)) {
    http_response_code(404);
    require __DIR__ . '/views/errors/404.php';
    exit;
}

$controller->$action();
