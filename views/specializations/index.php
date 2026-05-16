<?php
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
require __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../../core/CSRF.php';

$isEditing = !empty($editItem);
$formAction = $isEditing ? url('page=specializations&action=update') : url('page=specializations&action=store');
$formTitle = $isEditing ? 'Edit Specialization' : 'Add New Specialization';
$buttonText = $isEditing ? 'Update Specialization' : 'Add Specialization';
$nameValue = $isEditing ? ($editItem['name'] ?? '') : '';
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Specializations</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('page=dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Specializations</li>
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
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><?= e($formTitle) ?></h3>
                        </div>

                        <form method="post" action="<?= e($formAction) ?>">
                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                            <?php if ($isEditing): ?>
                                <input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>">
                            <?php endif; ?>

                            <div class="card-body">
                                <div class="form-group">
                                    <label for="specialization-name">Specialization Name</label>
                                    <input
                                        type="text"
                                        id="specialization-name"
                                        name="name"
                                        class="form-control"
                                        value="<?= e($nameValue) ?>"
                                        placeholder="Example: Cardiology"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><?= e($buttonText) ?></button>
                                <?php if ($isEditing): ?>
                                    <a href="<?= url('page=specializations') ?>" class="btn btn-secondary">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">All Specializations</h3>
                        </div>

                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">#</th>
                                        <th>Name</th>
                                        <th style="width: 180px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= e($item['name']) ?></td>
                                            <td>
                                                <a
                                                    href="<?= url('page=specializations&action=edit&id=' . (int)$item['id']) ?>"
                                                    class="btn btn-sm btn-info"
                                                >
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>

                                                <form
                                                    method="post"
                                                    action="<?= url('page=specializations&action=delete') ?>"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Delete this specialization?')"
                                                >
                                                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                                    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <?php if (empty($items)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No specializations found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
