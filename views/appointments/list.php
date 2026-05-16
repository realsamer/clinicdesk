<?php require __DIR__ . '/../partials/header.php'; ?>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<?php require __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>My Appointments</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('page=dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Appointments</li>
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
                    <h3 class="card-title">Filter Appointments</h3>
                    <div class="card-tools">
                        <a href="<?= url('page=appointments&action=book') ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-calendar-plus mr-1"></i> Book Appointment
                        </a>
                    </div>
                </div>
                <form method="get" action="index.php">
                    <input type="hidden" name="page" value="appointments">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?= e($filters['start_date'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?= e($filters['end_date'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <button type="submit" class="btn btn-primary btn-block">Apply Filter</button>
                                    <a href="<?= url('page=appointments') ?>" class="btn btn-default btn-block">Clear</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Appointment History</h3>
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
                                <th>Reason</th>
                                <th style="width: 170px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($appointments)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No appointments found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($appointments as $appointment): ?>
                                    <tr>
                                        <td><?= e($appointment['doctor_name']) ?></td>
                                        <td><?= e($appointment['specialization_name']) ?></td>
                                        <td><?= e(formatDate($appointment['appt_date'])) ?></td>
                                        <td><?= e(formatTime($appointment['appt_time'])) ?></td>
                                        <td><?= statusBadge($appointment['status']) ?></td>
                                        <td><?= e($appointment['reason'] ?: '-') ?></td>
                                        <td>
                                            <?php if ($appointment['status'] === 'completed' && !empty($appointment['prescription_id'])): ?>
                                                <a href="<?= url('page=prescriptions&action=download&id=' . (int)$appointment['id']) ?>" class="btn btn-xs btn-success mb-1">
                                                    View Prescription
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($appointment['status'] === 'pending'): ?>
                                                <form method="post" action="<?= url('page=appointments&action=cancel') ?>" class="d-inline" onsubmit="return confirm('Cancel this appointment?');">
                                                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                                    <input type="hidden" name="id" value="<?= (int)$appointment['id'] ?>">
                                                    <button type="submit" class="btn btn-xs btn-danger">Cancel</button>
                                                </form>
                                            <?php elseif ($appointment['status'] !== 'completed' || empty($appointment['prescription_id'])): ?>
                                                <span class="text-muted small">No action</span>
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
