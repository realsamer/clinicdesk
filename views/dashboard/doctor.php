<?php require __DIR__ . '/../partials/header.php'; ?>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<?php require __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Doctor Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('page=dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Doctor</li>
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
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= (int)($monthlyStats['total'] ?? 0) ?></h3>
                            <p>Total This Month</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= (int)($monthlyStats['pending'] ?? 0) ?></h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= (int)($monthlyStats['completed'] ?? 0) ?></h3>
                            <p>Completed</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?= count($todayAppointments) ?></h3>
                            <p>Today</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-day"></i></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Today's Appointments</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Patient</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($todayAppointments)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No appointments today.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($todayAppointments as $appointment): ?>
                                            <tr>
                                                <td><?= e(formatTime($appointment['appt_time'])) ?></td>
                                                <td><?= e($appointment['patient_name']) ?></td>
                                                <td><?= statusBadge($appointment['status']) ?></td>
                                                <td><a href="<?= url('page=appointments&action=detail&id=' . (int)$appointment['id']) ?>" class="btn btn-xs btn-primary">Open</a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Next 5 Upcoming Appointments</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Patient</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($upcomingAppointments)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No upcoming appointments.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($upcomingAppointments as $appointment): ?>
                                            <tr>
                                                <td><?= e(formatDate($appointment['appt_date'])) ?></td>
                                                <td><?= e(formatTime($appointment['appt_time'])) ?></td>
                                                <td><?= e($appointment['patient_name']) ?></td>
                                                <td><?= statusBadge($appointment['status']) ?></td>
                                                <td><a href="<?= url('page=appointments&action=detail&id=' . (int)$appointment['id']) ?>" class="btn btn-xs btn-primary">Open</a></td>
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
