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
                    <h1>Edit User</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('page=dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('page=users') ?>">Users</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card">
                <form method="post" enctype="multipart/form-data" action="<?= url('page=users&action=update') ?>">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">

                    <div class="card-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input name="name" value="<?= e($user['name']) ?>" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input value="<?= e($user['email']) ?>" class="form-control" disabled>
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <input value="<?= e(ucfirst($user['role'])) ?>" class="form-control" disabled>
                        </div>

                        <div class="form-group">
                            <label>Phone</label>
                            <input name="phone" value="<?= e($user['phone'] ?? '') ?>" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>User Avatar (JPEG/PNG, max 1MB)</label>
                            <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png">
                        </div>

                        <?php if (!empty($user['avatar'])): ?>
                            <div class="form-group">
                                <label>Current Avatar</label><br>
                                <img src="<?= e($user['avatar']) ?>" alt="User avatar" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                            </div>
                        <?php endif; ?>

                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="active" <?= (int)$user['is_active'] === 1 ? 'checked' : '' ?> <?= (int)$user['id'] === Auth::id() ? 'disabled' : '' ?>>
                            <label for="active" class="form-check-label">Active account</label>
                            <?php if ((int)$user['id'] === Auth::id()): ?>
                                <small class="form-text text-muted">You cannot deactivate your own account.</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn-primary">Save</button>
                        <a href="<?= url('page=users') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
