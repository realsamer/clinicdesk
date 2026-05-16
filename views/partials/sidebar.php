<?php
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/helpers.php';

$currentPage = $_GET['page'] ?? 'home';
$currentAction = $_GET['action'] ?? 'index';
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?= url('page=dashboard') ?>" class="brand-link text-center">
        <span class="brand-text font-weight-light"><?= e(APP_NAME) ?></span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="<?= url('page=dashboard') ?>" class="nav-link <?= in_array($currentPage, ['home', 'dashboard'], true) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php if (Auth::role() === 'admin'): ?>
                    <li class="nav-item">
                        <a href="<?= url('page=users') ?>" class="nav-link <?= $currentPage === 'users' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('page=doctors') ?>" class="nav-link <?= $currentPage === 'doctors' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-user-md"></i>
                            <p>Doctors</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('page=specializations') ?>" class="nav-link <?= $currentPage === 'specializations' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-stethoscope"></i>
                            <p>Specializations</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('page=appointments') ?>" class="nav-link <?= $currentPage === 'appointments' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>All Appointments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('page=reports') ?>" class="nav-link <?= $currentPage === 'reports' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-file-csv"></i>
                            <p>Reports</p>
                        </a>
                    </li>

                <?php elseif (Auth::role() === 'doctor'): ?>
                    <li class="nav-item">
                        <a href="<?= url('page=appointments') ?>" class="nav-link <?= $currentPage === 'appointments' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>My Schedule</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('page=doctors&action=profile') ?>" class="nav-link <?= ($currentPage === 'doctors' && $currentAction === 'profile') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-user-cog"></i>
                            <p>My Profile</p>
                        </a>
                    </li>

                <?php elseif (Auth::role() === 'patient'): ?>
                    <?php
                    $isBookAppointment = $currentPage === 'appointments' && $currentAction === 'book';
                    $isMyAppointments = $currentPage === 'appointments' && !$isBookAppointment;
                    $isMyPrescriptions = $currentPage === 'prescriptions';
                    $isMyProfile = $currentPage === 'users' && $currentAction === 'profile';
                    ?>
                    <li class="nav-item">
                        <a href="<?= url('page=appointments&action=book') ?>" class="nav-link <?= $isBookAppointment ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-plus"></i>
                            <p>Book Appointment</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('page=appointments') ?>" class="nav-link <?= $isMyAppointments ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-list"></i>
                            <p>My Appointments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('page=prescriptions') ?>" class="nav-link <?= $isMyPrescriptions ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-file-medical"></i>
                            <p>My Prescriptions</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('page=users&action=profile') ?>" class="nav-link <?= $isMyProfile ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-user"></i>
                            <p>My Profile</p>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>
