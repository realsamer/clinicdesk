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
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . '/../partials/alerts.php'; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Report Filters</h3>
                </div>
                <form method="get" action="index.php">
                    <input type="hidden" name="page" value="reports">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?= e($filters['start_date'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?= e($filters['end_date'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="doctor_id">Doctor</label>
                                    <select name="doctor_id" id="doctor_id" class="form-control">
                                        <option value="">All Doctors</option>
                                        <?php foreach ($doctors as $doctor): ?>
                                            <option value="<?= (int)$doctor['id'] ?>" <?= (int)($filters['doctor_id'] ?? 0) === (int)$doctor['id'] ? 'selected' : '' ?>>
                                                <?= e($doctor['doctor_name']) ?> - <?= e($doctor['specialization_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <?php foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status): ?>
                                            <option value="<?= e($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>>
                                                <?= e(ucfirst($status)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i> Generate Report
                        </button>
                        <a href="<?= url('page=reports') ?>" class="btn btn-default">Clear</a>

                        <?php if (!empty($reportRows)): ?>
                            <?php
                            $exportParams = $_GET;
                            $exportParams['page'] = 'reports';
                            $exportParams['export'] = 'csv';
                            ?>
                            <a href="index.php?<?= e(http_build_query($exportParams)) ?>" class="btn btn-success float-right">
                                <i class="fas fa-file-csv mr-1"></i> Export CSV
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <?php if ($hasSubmitted && empty($errors)): ?>
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary"><i class="fas fa-list"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Shown</span>
                                <span class="info-box-number"><?= (int)$totalShown ?></span>
                            </div>
                        </div>
                    </div>
                    <?php foreach ($statusSummary as $status => $count): ?>
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-secondary"><i class="fas fa-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?= e(ucfirst($status)) ?></span>
                                    <span class="info-box-number"><?= (int)$count ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Report Results</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Doctor Name</th>
                                    <th>Specialization</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reportRows)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No appointments found for the selected filters.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reportRows as $row): ?>
                                        <tr>
                                            <td><?= e($row['patient_name']) ?></td>
                                            <td><?= e($row['doctor_name']) ?></td>
                                            <td><?= e($row['specialization_name']) ?></td>
                                            <td><?= e(formatDate($row['appt_date'])) ?></td>
                                            <td><?= e(formatTime($row['appt_time'])) ?></td>
                                            <td><?= statusBadge($row['status']) ?></td>
                                            <td><?= e($row['reason'] ?: '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7">
                                        Total shown: <?= (int)$totalShown ?> |
                                        Pending: <?= (int)$statusSummary['pending'] ?> |
                                        Confirmed: <?= (int)$statusSummary['confirmed'] ?> |
                                        Completed: <?= (int)$statusSummary['completed'] ?> |
                                        Cancelled: <?= (int)$statusSummary['cancelled'] ?>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    Choose a start date and end date, then click <strong>Generate Report</strong>.
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
