<?php
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
require __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../../core/CSRF.php';
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Change User Password</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('page=dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('page=users') ?>">Users</a></li>
                        <li class="breadcrumb-item active">Password</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card">
                <form method="post" action="<?= url('page=users&action=update_password') ?>">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">

                    <div class="card-body">
                        <p>User: <strong><?= e($user['name']) ?></strong></p>
                        <p>Email: <strong><?= e($user['email']) ?></strong></p>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="form-control" minlength="8" required>
                            <small class="text-muted">Minimum 8 characters.</small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn-primary">Update Password</button>
                        <a href="<?= url('page=users') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
