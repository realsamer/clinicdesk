<?php require __DIR__ . '/../partials/header.php'; ?>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<?php require __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Add Prescription</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('page=dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('page=appointments') ?>">Appointments</a></li>
                        <li class="breadcrumb-item active">Add Prescription</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Prescription for <?= e($appointment['patient_name']) ?></h3>
                </div>

                <form method="post" action="<?= url('page=prescriptions&action=store') ?>" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= e(CSRF::generateToken()) ?>">
                    <input type="hidden" name="appointment_id" value="<?= (int)$appointment['id'] ?>">

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

                        <div class="form-group">
                            <label for="diagnosis">Diagnosis <span class="text-danger">*</span></label>
                            <textarea name="diagnosis" id="diagnosis" rows="4" class="form-control" required><?= old('diagnosis') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="medications">Medications <span class="text-danger">*</span></label>
                            <textarea name="medications" id="medications" rows="4" class="form-control" required><?= old('medications') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control"><?= old('notes') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="prescription_file">Scanned Prescription PDF</label>
                            <input type="file" name="prescription_file" id="prescription_file" class="form-control" accept="application/pdf,.pdf">
                            <small class="form-text text-muted">Optional. PDF only. Maximum size: 3MB.</small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Prescription</button>
                        <a href="<?= url('page=appointments&action=detail&id=' . (int)$appointment['id']) ?>" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
