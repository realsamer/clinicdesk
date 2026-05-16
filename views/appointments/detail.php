<?php require __DIR__ . '/../partials/header.php'; ?>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<?php require __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Appointment Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('page=dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('page=appointments') ?>">Appointments</a></li>
                        <li class="breadcrumb-item active">Details</li>
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
                    <h3 class="card-title">Appointment #<?= (int)$appointment['id'] ?></h3>
                    <div class="card-tools">
                        <a href="<?= url('page=appointments') ?>" class="btn btn-sm btn-default">Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Patient:</strong> <?= e($appointment['patient_name']) ?></p>
                            <p><strong>Doctor:</strong> <?= e($appointment['doctor_name']) ?></p>
                            <p><strong>Specialization:</strong> <?= e($appointment['specialization_name']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Date:</strong> <?= e(formatDate($appointment['appt_date'])) ?></p>
                            <p><strong>Time:</strong> <?= e(formatTime($appointment['appt_time'])) ?></p>
                            <p><strong>Status:</strong> <?= statusBadge($appointment['status']) ?></p>
                        </div>
                    </div>

                    <hr>

                    <p><strong>Reason:</strong></p>
                    <p><?= e($appointment['reason'] ?: '-') ?></p>

                    <p><strong>Doctor Notes:</strong></p>
                    <p><?= nl2br(e($appointment['doctor_notes'] ?: '-')) ?></p>

                    <?php if ($appointment['status'] === 'completed' && !empty($appointment['prescription_id'])): ?>
                        <a href="<?= url('page=prescriptions&action=download&id=' . (int)$appointment['id']) ?>" class="btn btn-success">
                            <i class="fas fa-file-medical mr-1"></i> Download Prescription
                        </a>
                    <?php endif; ?>

                    <?php if (Auth::role() === 'doctor' && $appointment['status'] === 'completed' && empty($appointment['prescription_id'])): ?>
                        <a href="<?= url('page=prescriptions&action=add&appointment_id=' . (int)$appointment['id']) ?>" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i> Add Prescription
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (Auth::role() === 'doctor'): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Doctor Actions</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($appointment['status'] === 'pending'): ?>
                            <form method="post" action="<?= url('page=appointments&action=status') ?>" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                <input type="hidden" name="id" value="<?= (int)$appointment['id'] ?>">
                                <input type="hidden" name="status" value="confirmed">
                                <input type="hidden" name="doctor_notes" value="<?= e($appointment['doctor_notes'] ?? '') ?>">
                                <button type="submit" class="btn btn-info">Confirm Appointment</button>
                            </form>
                            <form method="post" action="<?= url('page=appointments&action=status') ?>" class="d-inline" onsubmit="return confirm('Cancel this appointment?');">
                                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                <input type="hidden" name="id" value="<?= (int)$appointment['id'] ?>">
                                <input type="hidden" name="status" value="cancelled">
                                <input type="hidden" name="doctor_notes" value="<?= e($appointment['doctor_notes'] ?? '') ?>">
                                <button type="submit" class="btn btn-danger">Cancel Appointment</button>
                            </form>
                        <?php elseif ($appointment['status'] === 'confirmed'): ?>
                            <form method="post" action="<?= url('page=appointments&action=status') ?>" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                <input type="hidden" name="id" value="<?= (int)$appointment['id'] ?>">
                                <input type="hidden" name="status" value="completed">
                                <input type="hidden" name="doctor_notes" value="<?= e($appointment['doctor_notes'] ?? '') ?>">
                                <button type="submit" class="btn btn-success">Mark as Completed</button>
                            </form>
                            <form method="post" action="<?= url('page=appointments&action=status') ?>" class="d-inline" onsubmit="return confirm('Cancel this appointment?');">
                                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                <input type="hidden" name="id" value="<?= (int)$appointment['id'] ?>">
                                <input type="hidden" name="status" value="cancelled">
                                <input type="hidden" name="doctor_notes" value="<?= e($appointment['doctor_notes'] ?? '') ?>">
                                <button type="submit" class="btn btn-danger">Cancel Appointment</button>
                            </form>
                        <?php else: ?>
                            <p class="text-muted mb-0">No status actions are available for this appointment.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Update Doctor Notes</h3>
                    </div>
                    <form method="post" action="<?= url('page=appointments&action=notes') ?>">
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                        <input type="hidden" name="id" value="<?= (int)$appointment['id'] ?>">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="doctor_notes">Notes</label>
                                <textarea name="doctor_notes" id="doctor_notes" rows="5" class="form-control" placeholder="Write private notes about this appointment"><?= e($appointment['doctor_notes'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Save Notes</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
