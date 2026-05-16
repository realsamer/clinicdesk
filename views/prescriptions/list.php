<?php require __DIR__ . '/../partials/header.php'; ?>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<?php require __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>My Prescriptions</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('page=dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Prescriptions</li>
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
                    <h3 class="card-title">Completed Appointment Prescriptions</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Specialization</th>
                                <th>Date</th>
                                <th>Diagnosis</th>
                                <th>Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$items): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No prescriptions found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($items as $row): ?>
                                    <tr>
                                        <td><?= e($row['doctor_name']) ?></td>
                                        <td><?= e($row['specialization_name']) ?></td>
                                        <td><?= e(formatDate($row['appt_date'])) ?> <?= e(formatTime($row['appt_time'])) ?></td>
                                        <td><?= e(strlen($row['diagnosis']) > 80 ? substr($row['diagnosis'], 0, 80) . '...' : $row['diagnosis']) ?></td>
                                        <td>
                                            <?php if (!empty($row['file_path'])): ?>
                                                <a href="<?= url('page=prescriptions&action=download&id=' . (int)$row['appointment_id']) ?>" class="btn btn-sm btn-success">
                                                    Download PDF
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">No file</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
