<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../models/PrescriptionModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

class PrescriptionController
{
    private PrescriptionModel $prescriptions;
    private AppointmentModel $appointments;
    private DoctorModel $doctors;

    public function __construct()
    {
        $this->prescriptions = new PrescriptionModel();
        $this->appointments = new AppointmentModel();
        $this->doctors = new DoctorModel();
    }

    public function index(): void
    {
        Auth::requireRole('patient');

        $items = $this->prescriptions->getByPatient(Auth::id());
        $pageTitle = 'My Prescriptions';

        require __DIR__ . '/../views/prescriptions/list.php';
    }

    public function add(): void
    {
        Auth::requireRole('doctor');

        $appointmentId = (int)($_GET['appointment_id'] ?? 0);
        $appointment = $this->appointments->findById($appointmentId);

        $this->validateDoctorCanAdd($appointment);

        $pageTitle = 'Add Prescription';
        require __DIR__ . '/../views/prescriptions/add.php';
    }

    public function store(): void
    {
        Auth::requireRole('doctor');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token.');
            redirect(url('page=appointments'));
        }

        $appointmentId = (int)($_POST['appointment_id'] ?? 0);
        $appointment = $this->appointments->findById($appointmentId);

        $this->validateDoctorCanAdd($appointment);

        $diagnosis = sanitize($_POST['diagnosis'] ?? '');
        $medications = sanitize($_POST['medications'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');

        if ($diagnosis === '' || $medications === '') {
            flash('danger', 'Diagnosis and medications are required.');
            redirect(url('page=prescriptions&action=add&appointment_id=' . $appointmentId));
        }

        $filePath = $this->handlePdfUpload($appointmentId);

        $newId = $this->prescriptions->create([
            'appointment_id' => $appointmentId,
            'diagnosis' => $diagnosis,
            'medications' => $medications,
            'notes' => $notes,
            'file_path' => $filePath,
        ]);

        if ($newId < 1) {
            flash('danger', 'Could not save prescription. Please try again.');
            redirect(url('page=prescriptions&action=add&appointment_id=' . $appointmentId));
        }

        flash('success', 'Prescription added successfully.');
        redirect(url('page=appointments&action=detail&id=' . $appointmentId));
    }

    public function download(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');

        $appointmentId = (int)($_GET['id'] ?? 0);
        $prescription = $this->prescriptions->findByAppointmentId($appointmentId);

        if (!$prescription || empty($prescription['file_path'])) {
            flash('danger', 'Prescription file was not found.');
            redirect(url('page=appointments'));
        }

        if (!$this->canDownload($prescription)) {
            redirect(url('page=error&action=403'));
        }

        $absolutePath = ROOT_PATH . '/' . $prescription['file_path'];

        if (!is_file($absolutePath)) {
            flash('danger', 'Prescription file is missing from the server.');
            redirect(url('page=appointments'));
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="prescription.pdf"');
        header('Content-Length: ' . filesize($absolutePath));
        readfile($absolutePath);
        exit;
    }

    private function validateDoctorCanAdd(?array $appointment): void
    {
        if (!$appointment) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            exit;
        }

        $doctor = $this->doctors->findByUserId(Auth::id());

        if (!$doctor || (int)$appointment['doctor_id'] !== (int)$doctor['id']) {
            redirect(url('page=error&action=403'));
        }

        if ($appointment['status'] !== 'completed') {
            flash('danger', 'Prescription can be added only after appointment is completed.');
            redirect(url('page=appointments&action=detail&id=' . (int)$appointment['id']));
        }

        if ($this->prescriptions->findByAppointmentId((int)$appointment['id'])) {
            flash('danger', 'This appointment already has a prescription.');
            redirect(url('page=appointments&action=detail&id=' . (int)$appointment['id']));
        }
    }

    private function canDownload(array $prescription): bool
    {
        if (Auth::role() === 'admin') {
            return true;
        }

        if (Auth::role() === 'patient') {
            return (int)$prescription['patient_id'] === Auth::id();
        }

        if (Auth::role() === 'doctor') {
            $doctor = $this->doctors->findByUserId(Auth::id());

            return $doctor && (int)$prescription['doctor_id'] === (int)$doctor['id'];
        }

        return false;
    }

    private function handlePdfUpload(int $appointmentId): ?string
    {
        if (empty($_FILES['prescription_file']) || $_FILES['prescription_file']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($_FILES['prescription_file']['error'] !== UPLOAD_ERR_OK) {
            flash('danger', uploadErrorMessage($_FILES['prescription_file']['error']));
            redirect(url('page=prescriptions&action=add&appointment_id=' . $appointmentId));
        }

        if ($_FILES['prescription_file']['size'] > MAX_PDF_SIZE) {
            flash('danger', 'PDF must not exceed 3MB.');
            redirect(url('page=prescriptions&action=add&appointment_id=' . $appointmentId));
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['prescription_file']['tmp_name']);
        finfo_close($finfo);

        if ($mime !== 'application/pdf') {
            flash('danger', 'Only valid PDF files are allowed.');
            redirect(url('page=prescriptions&action=add&appointment_id=' . $appointmentId));
        }

        $filename = 'prescription_' . $appointmentId . '_' . time() . '.pdf';

        if (!is_dir(UPLOAD_PRESCRIPTION_PATH)) {
            mkdir(UPLOAD_PRESCRIPTION_PATH, 0775, true);
        }

        if (!move_uploaded_file($_FILES['prescription_file']['tmp_name'], UPLOAD_PRESCRIPTION_PATH . $filename)) {
            flash('danger', 'Could not save PDF file.');
            redirect(url('page=prescriptions&action=add&appointment_id=' . $appointmentId));
        }

        return 'public/uploads/prescriptions/' . $filename;
    }
}
