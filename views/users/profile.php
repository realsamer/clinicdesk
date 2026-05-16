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
                    <h1>My Profile</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('page=dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . '/../partials/alerts.php'; ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile text-center">
                            <?php if (!empty($user['avatar'])): ?>
                                <img src="<?= e($user['avatar']) ?>" alt="User avatar" class="profile-user-img img-fluid img-circle" style="width: 120px; height: 120px; object-fit: cover;">
                            <?php else: ?>
                                <div class="profile-user-img img-fluid img-circle d-flex align-items-center justify-content-center bg-light" style="width: 120px; height: 120px; margin: 0 auto;">
                                    <i class="fas fa-user fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>

                            <h3 class="profile-username text-center mt-3"><?= e($user['name']) ?></h3>
                            <p class="text-muted text-center"><?= e(ucfirst($user['role'])) ?></p>

                            <ul class="list-group list-group-unbordered mb-3 text-left">
                                <li class="list-group-item">
                                    <strong>Email</strong>
                                    <span class="float-right text-muted"><?= e($user['email']) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Phone</strong>
                                    <span class="float-right text-muted"><?= e($user['phone'] ?: 'Not set') ?></span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Status</strong>
                                    <span class="float-right badge badge-<?= (int)$user['is_active'] === 1 ? 'success' : 'danger' ?>">
                                        <?= (int)$user['is_active'] === 1 ? 'Active' : 'Inactive' ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Edit Profile</h3>
                        </div>

                        <form method="post" enctype="multipart/form-data" action="<?= url('page=users&action=update_profile') ?>">
                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                            <div class="card-body">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input name="name" value="<?= e($user['name']) ?>" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input value="<?= e($user['email']) ?>" class="form-control" disabled>
                                    <small class="form-text text-muted">Email cannot be changed from the patient profile page.</small>
                                </div>

                                <div class="form-group">
                                    <label>Phone</label>
                                    <input name="phone" value="<?= e($user['phone'] ?? '') ?>" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>Avatar (JPEG/PNG, max 1MB)</label>
                                    <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png">
                                    <small class="form-text text-muted">Leave this field empty to keep the current avatar.</small>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button class="btn btn-primary">Save Profile</button>
                                <a href="<?= url('page=dashboard') ?>" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
