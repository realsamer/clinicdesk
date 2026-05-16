<?php require __DIR__ . '/../partials/header.php'; ?>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<?php require __DIR__ . '/../partials/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Book Appointment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('page=dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('page=appointments') ?>">My Appointments</a></li>
                        <li class="breadcrumb-item active">Book</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . '/../partials/alerts.php'; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Appointment Details</h3>
                        </div>
                        <form method="post" action="<?= url('page=appointments&action=store') ?>">
                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                            <div class="card-body">
                                <div class="form-group">
                                    <label for="doctor_id">Doctor</label>
                                    <select name="doctor_id" id="doctor_id" class="form-control" required>
                                        <option value="">Choose doctor</option>
                                        <?php foreach ($doctors as $doctor): ?>
                                            <option value="<?= (int)$doctor['id'] ?>">
                                                <?= e($doctor['doctor_name']) ?> - <?= e($doctor['specialization_name']) ?>
                                                (Available: <?= e($doctor['available_days']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="appt_date">Preferred Date</label>
                                            <input
                                                type="date"
                                                name="appt_date"
                                                id="appt_date"
                                                class="form-control"
                                                min="<?= e(date('Y-m-d')) ?>"
                                                required
                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="appt_time">Time Slot</label>
                                            <select name="appt_time" id="appt_time" class="form-control" required>
                                                <option value="">Choose time</option>
                                                <?php foreach ($timeSlots as $slot): ?>
                                                    <option value="<?= e($slot) ?>"><?= e($slot) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="reason">Reason</label>
                                    <input
                                        type="text"
                                        name="reason"
                                        id="reason"
                                        class="form-control"
                                        maxlength="255"
                                        placeholder="Short reason for the visit"
                                    >
                                </div>
                            </div>

                            <div class="card-footer d-flex justify-content-between">
                                <a href="<?= url('page=appointments') ?>" class="btn btn-secondary">Back</a>
                                <button type="submit" class="btn btn-primary">Book Appointment</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Doctor Availability</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($doctors)): ?>
                                <p class="text-muted mb-0">No active doctors are available yet.</p>
                            <?php else: ?>
                                <?php foreach ($doctors as $doctor): ?>
                                    <div class="mb-3 pb-3 border-bottom">
                                        <strong><?= e($doctor['doctor_name']) ?></strong><br>
                                        <span class="text-muted"><?= e($doctor['specialization_name']) ?></span><br>
                                        <span class="badge badge-info mt-1">Fee: <?= e($doctor['consultation_fee']) ?></span>
                                        <div class="small mt-2">
                                            Available days: <?= e($doctor['available_days']) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="alert alert-light border">
                        Please choose a date that matches the selected doctor's available days. The system will also check if the time slot is already booked.
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
