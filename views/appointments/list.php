<?php $csrfToken = CSRF::generateToken(); ?>
<?php require __DIR__ . '/../partials/header.php'; ?>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<?php require __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= e($pageTitle) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('page=dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?= e($pageTitle) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . '/../partials/alerts.php'; ?>

            <?php if (Auth::role() === 'doctor'): ?>
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
                                    <th>Reason</th>
                                    <th style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($todayList)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No appointments scheduled for today.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($todayList as $appointment): ?>
                                        <tr>
                                            <td><?= e(formatTime($appointment['appt_time'])) ?></td>
                                            <td><?= e($appointment['patient_name']) ?></td>
                                            <td><?= statusBadge($appointment['status']) ?></td>
                                            <td><?= e($appointment['reason'] ?: '-') ?></td>
                                            <td>
                                                <a href="<?= url('page=appointments&action=detail&id=' . (int)$appointment['id']) ?>" class="btn btn-xs btn-primary">
                                                    Open
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Appointments</h3>
                    <?php if (Auth::role() === 'patient'): ?>
                        <div class="card-tools">
                            <a href="<?= url('page=appointments&action=book') ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-calendar-plus mr-1"></i> Book Appointment
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <form method="get" action="index.php">
                    <input type="hidden" name="page" value="appointments">
                    <div class="card-body">
                        <div class="row">
                            <?php if (Auth::role() === 'admin'): ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="doctor_id">Doctor</label>
                                        <select name="doctor_id" id="doctor_id" class="form-control">
                                            <option value="">All Doctors</option>
                                            <?php foreach (($doctors ?? []) as $doctor): ?>
                                                <option value="<?= (int)$doctor['id'] ?>" <?= (int)($filters['doctor_id'] ?? 0) === (int)$doctor['id'] ? 'selected' : '' ?>>
                                                    <?= e($doctor['doctor_name']) ?> - <?= e($doctor['specialization_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="patient_name">Patient Name</label>
                                        <input type="text" name="patient_name" id="patient_name" class="form-control" value="<?= e($filters['patient_name'] ?? '') ?>" placeholder="Search patient">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="col-md-<?= Auth::role() === 'admin' ? '2' : '3' ?>">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">All</option>
                                        <?php foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status): ?>
                                            <option value="<?= e($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>>
                                                <?= e(ucfirst($status)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-<?= Auth::role() === 'admin' ? '2' : '3' ?>">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?= e($filters['start_date'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-<?= Auth::role() === 'admin' ? '2' : '3' ?>">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?= e($filters['end_date'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-<?= Auth::role() === 'admin' ? '12' : '3' ?> d-flex align-items-end">
                                <div class="form-group <?= Auth::role() === 'admin' ? '' : 'w-100' ?>">
                                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                                    <a href="<?= url('page=appointments') ?>" class="btn btn-default">Clear</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <?php if (Auth::role() === 'admin'): ?>
                            All Appointments
                        <?php elseif (Auth::role() === 'doctor'): ?>
                            Full Schedule
                        <?php else: ?>
                            Appointment History
                        <?php endif; ?>
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <?php if (Auth::role() === 'doctor'): ?>
                                    <th>Patient</th>
                                <?php elseif (Auth::role() === 'admin'): ?>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Specialization</th>
                                <?php else: ?>
                                    <th>Doctor</th>
                                    <th>Specialization</th>
                                <?php endif; ?>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th style="width: 230px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($appointments)): ?>
                                <?php
                                $emptyColspan = 7;
                                if (Auth::role() === 'doctor') {
                                    $emptyColspan = 6;
                                } elseif (Auth::role() === 'admin') {
                                    $emptyColspan = 8;
                                }
                                ?>
                                <tr>
                                    <td colspan="<?= $emptyColspan ?>" class="text-center text-muted py-4">No appointments found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($appointments as $appointment): ?>
                                    <tr>
                                        <?php if (Auth::role() === 'doctor'): ?>
                                            <td><?= e($appointment['patient_name']) ?></td>
                                        <?php elseif (Auth::role() === 'admin'): ?>
                                            <td><?= e($appointment['patient_name']) ?></td>
                                            <td><?= e($appointment['doctor_name']) ?></td>
                                            <td><?= e($appointment['specialization_name']) ?></td>
                                        <?php else: ?>
                                            <td><?= e($appointment['doctor_name']) ?></td>
                                            <td><?= e($appointment['specialization_name']) ?></td>
                                        <?php endif; ?>
                                        <td><?= e(formatDate($appointment['appt_date'])) ?></td>
                                        <td><?= e(formatTime($appointment['appt_time'])) ?></td>
                                        <td><?= statusBadge($appointment['status']) ?></td>
                                        <td><?= e($appointment['reason'] ?: '-') ?></td>
                                        <td>
                                            <a href="<?= url('page=appointments&action=detail&id=' . (int)$appointment['id']) ?>" class="btn btn-xs btn-primary mb-1">
                                                View
                                            </a>

                                            <?php if (Auth::role() === 'doctor'): ?>
                                                <?php if ($appointment['status'] === 'pending'): ?>
                                                    <form method="post" action="<?= url('page=appointments&action=status') ?>" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                                        <input type="hidden" name="id" value="<?= (int)$appointment['id'] ?>">
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <input type="hidden" name="doctor_notes" value="<?= e($appointment['doctor_notes'] ?? '') ?>">
                                                        <button type="submit" class="btn btn-xs btn-info mb-1">Confirm</button>
                                                    </form>
                                                    <form method="post" action="<?= url('page=appointments&action=status') ?>" class="d-inline" onsubmit="return confirm('Cancel this appointment?');">
                                                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                                        <input type="hidden" name="id" value="<?= (int)$appointment['id'] ?>">
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <input type="hidden" name="doctor_notes" value="<?= e($appointment['doctor_notes'] ?? '') ?>">
                                                        <button type="submit" class="btn btn-xs btn-danger mb-1">Cancel</button>
                                                    </form>
                                                <?php elseif ($appointment['status'] === 'confirmed'): ?>
                                                    <form method="post" action="<?= url('page=appointments&action=status') ?>" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                                        <input type="hidden" name="id" value="<?= (int)$appointment['id'] ?>">
                                                        <input type="hidden" name="status" value="completed">
                                                        <input type="hidden" name="doctor_notes" value="<?= e($appointment['doctor_notes'] ?? '') ?>">
                                                        <button type="submit" class="btn btn-xs btn-success mb-1">Complete</button>
                                                    </form>
                                                    <form method="post" action="<?= url('page=appointments&action=status') ?>" class="d-inline" onsubmit="return confirm('Cancel this appointment?');">
                                                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                                        <input type="hidden" name="id" value="<?= (int)$appointment['id'] ?>">
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <input type="hidden" name="doctor_notes" value="<?= e($appointment['doctor_notes'] ?? '') ?>">
                                                        <button type="submit" class="btn btn-xs btn-danger mb-1">Cancel</button>
                                                    </form>
                                                <?php elseif ($appointment['status'] === 'completed' && empty($appointment['prescription_id'])): ?>
                                                    <a href="<?= url('page=prescriptions&action=add&appointment_id=' . (int)$appointment['id']) ?>" class="btn btn-xs btn-success mb-1">
                                                        Add Prescription
                                                    </a>
                                                <?php endif; ?>
                                            <?php elseif (Auth::role() === 'patient'): ?>
                                                <?php if ($appointment['status'] === 'completed' && !empty($appointment['prescription_id'])): ?>
                                                    <a href="<?= url('page=prescriptions&action=download&id=' . (int)$appointment['id']) ?>" class="btn btn-xs btn-success mb-1">
                                                        View Prescription
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($appointment['status'] === 'pending'): ?>
                                                    <form method="post" action="<?= url('page=appointments&action=cancel') ?>" class="d-inline" onsubmit="return confirm('Cancel this appointment?');">
                                                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                                        <input type="hidden" name="id" value="<?= (int)$appointment['id'] ?>">
                                                        <button type="submit" class="btn btn-xs btn-danger">Cancel</button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <?php require __DIR__ . '/../partials/paginator.php'; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
