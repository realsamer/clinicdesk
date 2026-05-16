<?php
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
?>
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= url('page=dashboard') ?>" class="nav-link"><?= e(APP_NAME) ?></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto align-items-center">
        <?php if (Auth::check()): ?>
            <li class="nav-item mr-3 text-muted small">
                Signed in as <?= e(Auth::currentUser()['name'] ?? 'User') ?>
                (<?= e(Auth::role()) ?>)
            </li>
            <li class="nav-item">
                <form method="post" action="<?= url('page=auth&action=logout') ?>" class="mb-0">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </button>
                </form>
            </li>
        <?php else: ?>
            <li class="nav-item text-muted small mr-3">Not signed in</li>
            <li class="nav-item">
                <a href="<?= url('page=auth&action=login') ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-sign-in-alt mr-1"></i> Login
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
