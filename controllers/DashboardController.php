<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/PrescriptionModel.php';

class DashboardController
{
    private UserModel $users;
    private DoctorModel $doctors;
    private AppointmentModel $appointments;
    private PrescriptionModel $prescriptions;

    public function __construct()
    {
        $this->users = new UserModel();
        $this->doctors = new DoctorModel();
        $this->appointments = new AppointmentModel();
        $this->prescriptions = new PrescriptionModel();
    }

    public function index(): void
    {
        Auth::requireLogin();

        if (Auth::role() === 'admin') {
            $this->admin();
            return;
        }

        if (Auth::role() === 'doctor') {
            $this->doctor();
            return;
        }

        if (Auth::role() === 'patient') {
            $this->patient();
            return;
        }

        redirect(url('page=error&action=403'));
    }

    private function admin(): void
    {
        Auth::requireRole('admin');

        $userTotals = $this->users->countByRole();
        $appointmentsToday = $this->appointments->countToday();
        $weeklyStatusCounts = $this->appointments->countThisWeekByStatus();
        $recentAppointments = $this->appointments->recent(5);

        $pageTitle = 'Admin Dashboard';
        require __DIR__ . '/../views/dashboard/admin.php';
    }

    private function doctor(): void
    {
        Auth::requireRole('doctor');

        $doctor = $this->doctors->findByUserId(Auth::id());
        if (!$doctor) {
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $todayAppointments = $this->appointments->todayByDoctor((int)$doctor['id']);
        $monthlyStats = $this->appointments->doctorMonthlyStats((int)$doctor['id']);
        $upcomingAppointments = $this->appointments->upcomingByDoctor((int)$doctor['id'], 5);

        $pageTitle = 'Doctor Dashboard';
        require __DIR__ . '/../views/dashboard/doctor.php';
    }

    private function patient(): void
    {
        Auth::requireRole('patient');

        $patientId = Auth::id();
        $activeAppointments = $this->appointments->activeByPatient($patientId);
        $completedAppointments = $this->appointments->countCompletedByPatient($patientId);
        $prescriptionCount = $this->prescriptions->countByPatient($patientId);
        $nextAppointment = $this->appointments->nextUpcomingByPatient($patientId);

        $pageTitle = 'Patient Dashboard';
        require __DIR__ . '/../views/dashboard/patient.php';
    }
}
