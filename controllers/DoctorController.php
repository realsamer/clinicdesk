<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/Paginator.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/SpecializationModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class DoctorController
{
    private DoctorModel $doctors;

    public function __construct()
    {
        $this->doctors = new DoctorModel();
    }

    public function index(): void
    {
        Auth::requireRole('admin');
        $page = max(1, (int)($_GET['p'] ?? 1));
        $total = $this->doctors->countAll();
        $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
        $doctors = $this->doctors->getAllPaginated($page);
        $pageTitle = 'Doctors';
        require __DIR__ . '/../views/doctors/list.php';
    }

    public function edit(): void
    {
        Auth::requireRole('admin');
        $id = (int)($_GET['id'] ?? 0);
        $doctor = $this->doctors->findById($id);
        if (!$doctor) {
            require __DIR__ . '/../views/errors/404.php';
            return;
        }
        $specializations = (new SpecializationModel())->getAll();
        $pageTitle = 'Edit Doctor';
        require __DIR__ . '/../views/doctors/edit.php';
    }

    public function profile(): void
    {
        Auth::requireRole('doctor');
        $doctor = $this->doctors->findByUserId(Auth::id());
        if (!$doctor) {
            require __DIR__ . '/../views/errors/404.php';
            return;
        }
        $specializations = (new SpecializationModel())->getAll();
        $pageTitle = 'My Profile';
        require __DIR__ . '/../views/doctors/profile.php';
    }

    public function update(): void
    {
        Auth::requireRole('admin', 'doctor');
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token.');
            redirect(url('page=doctors'));
        }
        $doctorId = (int)($_POST['id'] ?? 0);
        $doctor = $this->doctors->findById($doctorId);
        if (!$doctor) {
            require __DIR__ . '/../views/errors/404.php';
            return;
        }
        if (Auth::role() === 'doctor' && (int)$doctor['user_id'] !== Auth::id()) {
            redirect(url('page=error&action=403'));
        }

        $days = $_POST['available_days'] ?? [];
        $validDays = array_values(array_intersect(dayOptions(), (array)$days));
        if (!$validDays) $validDays = ['Sun'];

        $this->doctors->update($doctorId, [
            'specialization_id' => (int)($_POST['specialization_id'] ?? $doctor['specialization_id']),
            'bio' => sanitize($_POST['bio'] ?? ''),
            'consultation_fee' => (float)($_POST['consultation_fee'] ?? 0),
            'available_days' => implode(',', $validDays),
        ]);

        $photo = $this->handleDoctorPhotoUpload('photo');
        if ($photo !== null) {
            (new UserModel())->update((int)$doctor['user_id'], ['avatar' => $photo]);
        }

        flash('success', 'Doctor information updated.');
        if (Auth::role() === 'doctor') {
            redirect(url('page=doctors&action=profile'));
        }
        redirect(url('page=doctors'));
    }

    private function handleDoctorPhotoUpload(string $field): ?string
    {
        if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            flash('danger', uploadErrorMessage($_FILES[$field]['error']));
            return null;
        }
        if ($_FILES[$field]['size'] > MAX_IMAGE_SIZE) {
            flash('danger', 'Doctor photo must not exceed 1MB.');
            return null;
        }
        $info = getimagesize($_FILES[$field]['tmp_name']);
        if (!$info || !in_array($info['mime'], ['image/jpeg', 'image/png'], true)) {
            flash('danger', 'Doctor photo must be JPEG or PNG.');
            return null;
        }
        $extension = $info['mime'] === 'image/png' ? 'png' : 'jpg';
        $filename = 'doctor_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        if (!is_dir(UPLOAD_DOCTOR_PHOTO_PATH)) mkdir(UPLOAD_DOCTOR_PHOTO_PATH, 0775, true);
        if (!move_uploaded_file($_FILES[$field]['tmp_name'], UPLOAD_DOCTOR_PHOTO_PATH . $filename)) {
            flash('danger', 'Could not save doctor photo.');
            return null;
        }
        return 'public/uploads/doctor_photos/' . $filename;
    }
}