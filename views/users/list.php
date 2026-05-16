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
                    <h1>Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('page=dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Management</h3>
                    <a href="<?= url('page=users&action=create') ?>" class="btn btn-primary float-right">
                        <i class="fas fa-plus"></i> Create User
                    </a>
                </div>

                <div class="card-body">
                    <form method="get" class="mb-3">
                        <input type="hidden" name="page" value="users">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="role" class="form-control">
                                    <option value="">All Roles</option>
                                    <?php foreach (['admin', 'doctor', 'patient'] as $r): ?>
                                        <option value="<?= e($r) ?>" <?= $role === $r ? 'selected' : '' ?>><?= e(ucfirst($r)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="search" value="<?= e($search) ?>" class="form-control" placeholder="Search by name or email">
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-secondary">Filter</button>
                                <a href="<?= url('page=users') ?>" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Active</th>
                                    <th style="width: 240px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($user['avatar'])): ?>
                                                <img src="<?= e($user['avatar']) ?>" alt="Avatar" class="img-circle mr-2" style="width: 32px; height: 32px; object-fit: cover;">
                                            <?php endif; ?>
                                            <?= e($user['name']) ?>
                                        </td>
                                        <td><?= e($user['email']) ?></td>
                                        <td><span class="badge badge-info"><?= e(ucfirst($user['role'])) ?></span></td>
                                        <td><?= e($user['phone'] ?? '') ?></td>
                                        <td>
                                            <?= (int)$user['is_active'] === 1 ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>' ?>
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-info" href="<?= url('page=users&action=edit&id=' . (int)$user['id']) ?>">Edit</a>
                                            <a class="btn btn-sm btn-warning" href="<?= url('page=users&action=password&id=' . (int)$user['id']) ?>">Password</a>
                                            <?php if ((int)$user['id'] !== Auth::id()): ?>
                                                <form method="post" action="<?= url('page=users&action=toggle') ?>" style="display: inline-block;">
                                                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                                    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Change this account status?')">
                                                        <?= (int)$user['is_active'] === 1 ? 'Deactivate' : 'Activate' ?>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (!$users): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No users found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php require __DIR__ . '/../partials/paginator.php'; ?>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
