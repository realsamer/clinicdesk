<?php require __DIR__ . '/../partials/header.php'; ?>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<?php require __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Admin Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('page=dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Admin</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . '/../partials/alerts.php'; ?>

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?= (int)($userTotals['admin'] ?? 0) ?></h3>
                            <p>Administrators</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-shield"></i></div>
                        <a href="<?= url('page=users&role=admin') ?>" class="small-box-footer">View admins <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= (int)($userTotals['doctor'] ?? 0) ?></h3>
                            <p>Doctors</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-md"></i></div>
                        <a href="<?= url('page=doctors') ?>" class="small-box-footer">View doctors <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= (int)($userTotals['patient'] ?? 0) ?></h3>
                            <p>Patients</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="<?= url('page=users&role=patient') ?>" class="small-box-footer">View patients <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= (int)$appointmentsToday ?></h3>
                            <p>Appointments Today</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-day"></i></div>
                        <a href="<?= url('page=appointments&start_date=' . date('Y-m-d') . '&end_date=' . date('Y-m-d')) ?>" class="small-box-footer">View today <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">This Week by Status</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status): ?>
                                        <tr>
                                            <td><?= statusBadge($status) ?></td>
                                            <td class="text-right"><strong><?= (int)($weeklyStatusCounts[$status] ?? 0) ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent 5 Appointments</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentAppointments)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No recent appointments found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentAppointments as $appointment): ?>
                                            <tr>
                                                <td><?= e($appointment['patient_name']) ?></td>
                                                <td><?= e($appointment['doctor_name']) ?></td>
                                                <td><?= e(formatDate($appointment['appt_date'])) ?></td>
                                                <td><?= e(formatTime($appointment['appt_time'])) ?></td>
                                                <td><?= statusBadge($appointment['status']) ?></td>
                                                <td>
                                                    <a href="<?= url('page=appointments&action=detail&id=' . (int)$appointment['id']) ?>" class="btn btn-xs btn-primary">Open</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
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
