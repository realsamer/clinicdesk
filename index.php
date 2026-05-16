<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/Auth.php';

$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

Auth::enforceFirstLogin($page, $action);

$routes = [
    'auth' => 'AuthController',
    'specializations' => 'SpecializationController',
    'doctors' => 'DoctorController',
    'users' => 'UserController',
    'appointments' => 'AppointmentController',
    // Other controllers will be added in later steps.
];

if ($page === 'dashboard' || $page === 'home') {
    Auth::requireLogin();

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
                            <li class="breadcrumb-item active">Dashboard</li>
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
                                <h3><?= e(Auth::role()) ?></h3>
                                <p>Current Role</p>
                            </div>
                            <div class="icon"><i class="fas fa-user-shield"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>Login</h3>
                                <p>Session Protected</p>
                            </div>
                            <div class="icon"><i class="fas fa-lock"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>CSRF</h3>
                                <p>Forms Protected</p>
                            </div>
                            <div class="icon"><i class="fas fa-shield-alt"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>Admin</h3>
                                <p>Step 13 Complete</p>
                            </div>
                            <div class="icon"><i class="fas fa-key"></i></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Authentication Connected</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">You are signed in as <strong><?= e(Auth::currentUser()['name'] ?? 'User') ?></strong>.</p>
                        <p class="mb-0">Patient, doctor, and admin appointment management are now available from the sidebar.</p>
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
