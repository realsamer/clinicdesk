<?php
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
require __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../../core/CSRF.php';
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create User</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('page=dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('page=users') ?>">Users</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card">
                <form method="post" enctype="multipart/form-data" action="<?= url('page=users&action=store') ?>">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input name="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Temporary Password</label>
                                    <input type="password" name="password" class="form-control" minlength="8" required>
                                    <small class="text-muted">Minimum 8 characters.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Role</label>
                                    <select name="role" id="role" class="form-control">
                                        <option value="patient">Patient</option>
                                        <option value="doctor">Doctor</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input name="phone" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>User Avatar (JPEG/PNG, max 1MB)</label>
                            <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png">
                        </div>

                        <div id="doctor-fields" class="card card-outline card-info mt-3" style="display: none;">
                            <div class="card-header">
                                <h3 class="card-title">Doctor Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Specialization</label>
                                            <select name="specialization_id" class="form-control">
                                                <?php foreach ($specializations as $specialization): ?>
                                                    <option value="<?= (int)$specialization['id'] ?>"><?= e($specialization['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Consultation Fee</label>
                                            <input type="number" step="0.01" name="consultation_fee" value="0" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Available Days</label><br>
                                    <?php foreach (dayOptions() as $day): ?>
                                        <label class="mr-3">
                                            <input type="checkbox" name="available_days[]" value="<?= e($day) ?>" <?= in_array($day, ['Sun', 'Mon', 'Tue', 'Wed', 'Thu'], true) ? 'checked' : '' ?>>
                                            <?= e($day) ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>

                                <div class="form-group">
                                    <label>Bio</label>
                                    <textarea name="bio" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn-primary">Create User</button>
                        <a href="<?= url('page=users') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var roleSelect = document.getElementById('role');
    var doctorFields = document.getElementById('doctor-fields');

    function toggleDoctorFields() {
        doctorFields.style.display = roleSelect.value === 'doctor' ? 'block' : 'none';
    }

    roleSelect.addEventListener('change', toggleDoctorFields);
    toggleDoctorFields();
});
</script>
<?php require __DIR__ . '/../partials/footer.php'; ?>
