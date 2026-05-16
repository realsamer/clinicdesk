<?php require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
require __DIR__ . '/../partials/sidebar.php'; ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Doctors</h1>
    </section>
    <section class="content">
        <?php require __DIR__ . '/../partials/alerts.php'; ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Doctor Records</h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Specialization</th>
                            <th>Fee</th>
                            <th>Availability</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($doctors as $doctor): ?>
                        <tr>
                            <td><?= e($doctor['doctor_name']) ?></td>
                            <td><?= e($doctor['email']) ?></td>
                            <td><?= e($doctor['specialization_name']) ?></td>
                            <td><?= e($doctor['consultation_fee']) ?></td>
                            <td><?= e($doctor['available_days']) ?></td>
                            <td><a href="<?= url('page=doctors&action=edit&id=' . $doctor['id']) ?>"
                                    class="btn btn-sm btn-info">Edit</a></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (!$doctors): ?><tr>
                            <td colspan="6">No doctors found.</td>
                        </tr><?php endif; ?>
                    </tbody>
                </table>
                <?php require __DIR__ . '/../partials/paginator.php'; ?>
            </div>
        </div>
    </section>
</div><?php require __DIR__ . '/../partials/footer.php'; ?>