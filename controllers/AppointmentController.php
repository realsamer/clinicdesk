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
        Auth::requireRole('patient');

        $page = max(1, (int)($_GET['p'] ?? 1));
        $filters = $this->readFilters();

        $total = $this->appointments->countByPatient(Auth::id(), $filters);
        $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
        $appointments = $this->appointments->getByPatient(Auth::id(), $page, $filters);

        $pageTitle = 'My Appointments';
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
}
