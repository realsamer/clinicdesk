<?php require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
require __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../../core/CSRF.php'; ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Doctor</h1>
    </section>
    <section class="content">
        <?php require __DIR__ . '/../partials/alerts.php'; ?>
        <div class="card">
            <form method="post" enctype="multipart/form-data" action="<?= url('page=doctors&action=update') ?>"><input
                    type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>"><input type="hidden" name="id"
                    value="<?= (int)$doctor['id'] ?>">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group"><label>Doctor Name</label><input class="form-control"
                                    value="<?= e($doctor['doctor_name']) ?>" disabled></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"><label>Email</label><input class="form-control"
                                    value="<?= e($doctor['email']) ?>" disabled></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group"><label>Specialization</label><select name="specialization_id"
                                    class="form-control"><?php foreach ($specializations as $s): ?><option
                                        value="<?= (int)$s['id'] ?>"
                                        <?= (int)$doctor['specialization_id'] === (int)$s['id'] ? 'selected' : '' ?>>
                                        <?= e($s['name']) ?></option><?php endforeach; ?></select></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"><label>Consultation Fee</label><input type="number" step="0.01"
                                    name="consultation_fee" value="<?= e($doctor['consultation_fee']) ?>"
                                    class="form-control"></div>
                        </div>
                    </div>
                    <div class="form-group"><label>Available Days</label><br><?php $selectedDays = explode(',', $doctor['available_days']);
                                                                                foreach (dayOptions() as $d): ?><label
                            class="mr-2"><input type="checkbox" name="available_days[]" value="<?= $d ?>"
                                <?= in_array($d, $selectedDays, true) ? 'checked' : '' ?>>
                            <?= $d ?></label><?php endforeach; ?></div>
                    <div class="form-group"><label>Bio</label><textarea name="bio" class="form-control"
                            rows="4"><?= e($doctor['bio']) ?></textarea></div>
                    <div class="form-group"><label>Doctor Photo (JPEG/PNG, max 1MB)</label><input type="file"
                            name="photo" class="form-control"></div>
                    <?php if (!empty($doctor['avatar'])): ?><p>Current photo: <img src="<?= e($doctor['avatar']) ?>"
                            alt="Doctor photo" class="avatar-preview"></p><?php endif; ?>
                </div>
                <div class="card-footer"><button class="btn btn-primary">Save</button><a
                        href="<?= url('page=doctors') ?>" class="btn btn-secondary">Cancel</a></div>
            </form>
        </div>
    </section>
</div><?php require __DIR__ . '/../partials/footer.php'; ?>