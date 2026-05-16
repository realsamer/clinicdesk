<?php require __DIR__ . '/../partials/header.php'; ?>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<?php require __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Patient Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('page=dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Patient</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . '/../partials/alerts.php'; ?>

            <?php if ($nextAppointment): ?>
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Next Upcoming Appointment</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="mb-2"><?= e($nextAppointment['doctor_name']) ?> - <?= e($nextAppointment['specialization_name']) ?></h5>
                        <p class="mb-1"><strong>Date:</strong> <?= e(formatDate($nextAppointment['appt_date'])) ?></p>
                        <p class="mb-1"><strong>Time:</strong> <?= e(formatTime($nextAppointment['appt_time'])) ?></p>
                        <p class="mb-0"><strong>Status:</strong> <?= statusBadge($nextAppointment['status']) ?></p>
                    </div>
                    <div class="card-footer">
                        <a href="<?= url('page=appointments&action=detail&id=' . (int)$nextAppointment['id']) ?>" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    You do not have an upcoming appointment. You can book a new appointment from the sidebar.
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= count($activeAppointments) ?></h3>
                            <p>Active Appointments</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-check"></i></div>
                        <a href="<?= url('page=appointments') ?>" class="small-box-footer">My appointments <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= (int)$completedAppointments ?></h3>
                            <p>Completed Appointments</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= (int)$prescriptionCount ?></h3>
                            <p>Prescriptions Available</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-medical"></i></div>
                        <a href="<?= url('page=prescriptions') ?>" class="small-box-footer">My prescriptions <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Active Appointments</h3>
                    <div class="card-tools">
                        <a href="<?= url('page=appointments&action=book') ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-calendar-plus mr-1"></i> Book Appointment
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Specialization</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($activeAppointments)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No active appointments found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($activeAppointments as $appointment): ?>
                                    <tr>
                                        <td><?= e($appointment['doctor_name']) ?></td>
                                        <td><?= e($appointment['specialization_name']) ?></td>
                                        <td><?= e(formatDate($appointment['appt_date'])) ?></td>
                                        <td><?= e(formatTime($appointment['appt_time'])) ?></td>
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
    </section>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
