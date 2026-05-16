<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/Auth.php';

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

$routes = [
    // Controllers will be added in later steps.
    // Example: 'auth' => 'AuthController',
];

if ($page === 'home' || $page === 'dashboard') {
    $pageTitle = 'Dashboard Preview';
    require __DIR__ . '/views/partials/header.php';
    require __DIR__ . '/views/partials/navbar.php';
    require __DIR__ . '/views/partials/sidebar.php';
    ?>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Dashboard Preview</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?= url('page=dashboard') ?>">Home</a></li>
                            <li class="breadcrumb-item active">Layout</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <?php require __DIR__ . '/views/partials/alerts.php'; ?>

                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>3</h3>
                                <p>User Roles</p>
                            </div>
                            <div class="icon"><i class="fas fa-users"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>5</h3>
                                <p>Database Tables</p>
                            </div>
                            <div class="icon"><i class="fas fa-database"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>CSRF</h3>
                                <p>POST Protection</p>
                            </div>
                            <div class="icon"><i class="fas fa-shield-alt"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>OOP</h3>
                                <p>Models Ready</p>
                            </div>
                            <div class="icon"><i class="fas fa-code"></i></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Step 5 Completed</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">The AdminLTE dashboard layout, local assets, and shared partial files were added successfully.</p>
                        <p class="mb-0">Authentication, models, controllers, and real dashboard statistics will be connected in the next steps.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php
    require __DIR__ . '/views/partials/footer.php';
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
