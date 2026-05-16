<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/Paginator.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

class AppointmentController
{
    private AppointmentModel $appointments;
    private DoctorModel $doctors;

    public function __construct()
    {
        $this->appointments = new AppointmentModel();
        $this->doctors = new DoctorModel();
    }

    public function index(): void
    {
        Auth::requireRole('doctor', 'patient');

        $page = max(1, (int)($_GET['p'] ?? 1));
        $filters = $this->readFilters();
        $todayList = [];

        if (Auth::role() === 'doctor') {
            $doctor = $this->currentDoctorOr403();
            $doctorId = (int)$doctor['id'];
            $total = $this->appointments->countFiltered('doctor', $doctorId, $filters);
            $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
            $appointments = $this->appointments->getByDoctor($doctorId, $page, $filters);
            $todayList = $this->appointments->todayByDoctor($doctorId);
            $pageTitle = 'My Schedule';
        } else {
            $total = $this->appointments->countByPatient(Auth::id(), $filters);
            $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
            $appointments = $this->appointments->getByPatient(Auth::id(), $page, $filters);
            $pageTitle = 'My Appointments';
        }

        require __DIR__ . '/../views/appointments/list.php';
    }

    public function book(): void
    {
        Auth::requireRole('patient');

        $doctors = $this->doctors->getAll();
        $timeSlots = fixedTimeSlots();
        $pageTitle = 'Book Appointment';

        require __DIR__ . '/../views/appointments/book.php';
    }

    public function store(): void
    {
        Auth::requireRole('patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token.');
            redirect(url('page=appointments&action=book'));
        }

        $doctorId = (int)($_POST['doctor_id'] ?? 0);
        $date = sanitize($_POST['appt_date'] ?? '');
        $time = sanitize($_POST['appt_time'] ?? '');
        $reason = sanitize($_POST['reason'] ?? '');

        $doctor = $this->doctors->findById($doctorId);
        if (!$doctor) {
            flash('danger', 'Please choose a valid doctor.');
            redirect(url('page=appointments&action=book'));
        }

        if (!$this->isValidDate($date)) {
            flash('danger', 'Please choose a valid appointment date.');
            redirect(url('page=appointments&action=book'));
        }

        if ($date < date('Y-m-d')) {
            flash('danger', 'Appointment date cannot be in the past.');
            redirect(url('page=appointments&action=book'));
        }

        if (!in_array($time, fixedTimeSlots(), true)) {
            flash('danger', 'Please choose a valid time slot.');
            redirect(url('page=appointments&action=book'));
        }

        $availableDays = array_filter(array_map('trim', explode(',', $doctor['available_days'] ?? '')));
        $selectedDay = date('D', strtotime($date));

        if (!in_array($selectedDay, $availableDays, true)) {
            flash('danger', 'The selected doctor is not available on ' . $selectedDay . '.');
            redirect(url('page=appointments&action=book'));
        }

        if ($this->appointments->hasConflict($doctorId, $date, $time)) {
            flash('danger', 'This slot is already booked, please choose another time.');
            redirect(url('page=appointments&action=book'));
        }

        $ok = $this->appointments->book([
            'patient_id' => Auth::id(),
            'doctor_id' => $doctorId,
            'appt_date' => $date,
            'appt_time' => $time,
            'status' => 'pending',
            'reason' => substr($reason, 0, 255),
        ]);

        if (!$ok) {
            flash('danger', 'Could not book appointment. The selected slot may already be taken.');
            redirect(url('page=appointments&action=book'));
        }

        flash('success', 'Appointment booked successfully. It is now pending doctor confirmation.');
        redirect(url('page=appointments'));
    }

    public function detail(): void
    {
        Auth::requireRole('doctor', 'patient');

        $appointment = $this->appointments->findById((int)($_GET['id'] ?? 0));

        if (!$appointment) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $this->authorizeAppointment($appointment);

        $pageTitle = 'Appointment Details';
        require __DIR__ . '/../views/appointments/detail.php';
    }

    public function status(): void
    {
        Auth::requireRole('doctor');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token.');
            redirect(url('page=appointments'));
        }

        $appointmentId = (int)($_POST['id'] ?? 0);
        $newStatus = sanitize($_POST['status'] ?? '');
        $doctorNotes = sanitize($_POST['doctor_notes'] ?? '');
        $appointment = $this->appointments->findById($appointmentId);

        if (!$appointment) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $this->authorizeDoctorAppointment($appointment);

        if (!in_array($newStatus, ['confirmed', 'completed', 'cancelled'], true)) {
            flash('danger', 'Invalid appointment status.');
            redirect(url('page=appointments&action=detail&id=' . $appointmentId));
        }

        if (!$this->isAllowedDoctorTransition($appointment['status'], $newStatus)) {
            flash('danger', 'This status change is not allowed.');
            redirect(url('page=appointments&action=detail&id=' . $appointmentId));
        }

        $this->appointments->updateStatus($appointmentId, $newStatus, $doctorNotes);
        flash('success', 'Appointment updated successfully.');
        redirect(url('page=appointments&action=detail&id=' . $appointmentId));
    }

    public function notes(): void
    {
        Auth::requireRole('doctor');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token.');
            redirect(url('page=appointments'));
        }

        $appointmentId = (int)($_POST['id'] ?? 0);
        $doctorNotes = sanitize($_POST['doctor_notes'] ?? '');
        $appointment = $this->appointments->findById($appointmentId);

        if (!$appointment) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $this->authorizeDoctorAppointment($appointment);
        $this->appointments->updateNotes($appointmentId, $doctorNotes);
        flash('success', 'Doctor notes saved successfully.');
        redirect(url('page=appointments&action=detail&id=' . $appointmentId));
    }

    public function cancel(): void
    {
        Auth::requireRole('patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token.');
            redirect(url('page=appointments'));
        }

        $appointmentId = (int)($_POST['id'] ?? 0);
        $appointment = $this->appointments->findById($appointmentId);

        if (!$appointment || (int)$appointment['patient_id'] !== Auth::id()) {
            redirect(url('page=error&action=403'));
        }

        if ($appointment['status'] !== 'pending') {
            flash('danger', 'Only pending appointments can be cancelled by the patient.');
            redirect(url('page=appointments'));
        }

        $this->appointments->cancelPendingByPatient($appointmentId, Auth::id());
        flash('success', 'Appointment cancelled successfully.');
        redirect(url('page=appointments'));
    }

    private function readFilters(): array
    {
        $status = sanitize($_GET['status'] ?? '');
        $startDate = sanitize($_GET['start_date'] ?? '');
        $endDate = sanitize($_GET['end_date'] ?? '');

        if (!in_array($status, ['pending', 'confirmed', 'completed', 'cancelled'], true)) {
            $status = '';
        }

        if (!$this->isValidDate($startDate)) {
            $startDate = '';
        }

        if (!$this->isValidDate($endDate)) {
            $endDate = '';
        }

        return [
            'status' => $status,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    private function isValidDate(string $date): bool
    {
        if ($date === '') {
            return false;
        }

        $parsed = DateTime::createFromFormat('Y-m-d', $date);

        return $parsed && $parsed->format('Y-m-d') === $date;
    }

    private function currentDoctorOr403(): array
    {
        $doctor = $this->doctors->findByUserId(Auth::id());

        if (!$doctor) {
            redirect(url('page=error&action=403'));
        }

        return $doctor;
    }

    private function authorizeAppointment(array $appointment): void
    {
        if (Auth::role() === 'patient' && (int)$appointment['patient_id'] === Auth::id()) {
            return;
        }

        if (Auth::role() === 'doctor') {
            $this->authorizeDoctorAppointment($appointment);
            return;
        }

        redirect(url('page=error&action=403'));
    }

    private function authorizeDoctorAppointment(array $appointment): void
    {
        $doctor = $this->currentDoctorOr403();

        if ((int)$appointment['doctor_id'] !== (int)$doctor['id']) {
            redirect(url('page=error&action=403'));
        }
    }

    private function isAllowedDoctorTransition(string $currentStatus, string $newStatus): bool
    {
        if ($currentStatus === 'pending' && in_array($newStatus, ['confirmed', 'cancelled'], true)) {
            return true;
        }

        if ($currentStatus === 'confirmed' && in_array($newStatus, ['completed', 'cancelled'], true)) {
            return true;
        }

        return false;
    }
}
