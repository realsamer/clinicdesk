<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

$routes = [
    // Controllers will be added in later steps.
    // Example: 'auth' => 'AuthController',
];

if ($page === 'home') {
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= e(APP_NAME) ?></title>
        <style>
            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: Arial, sans-serif;
                background: #f4f6f9;
                color: #333;
            }
            .box {
                width: 90%;
                max-width: 680px;
                padding: 30px;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
                text-align: center;
            }
            h1 {
                margin-top: 0;
                color: #007bff;
            }
            code {
                background: #f1f1f1;
                padding: 3px 6px;
                border-radius: 4px;
            }
        </style>
    </head>
    <body>
        <div class="box">
            <h1><?= e(APP_NAME) ?></h1>
            <p>Application bootstrap and routing shell loaded successfully.</p>
            <p>The database layer, authentication, controllers, and views will be added in the next steps.</p>
            <p>Try the routing error page: <code><?= e(url('page=unknown')) ?></code></p>
        </div>
    </body>
    </html>
    <?php
    exit;
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
