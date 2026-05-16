<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/Paginator.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/SpecializationModel.php';

class UserController
{
    private UserModel $users;
    private bool $uploadFailed = false;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function index(): void
    {
        Auth::requireRole('admin');

        $role = sanitize($_GET['role'] ?? '');
        $search = sanitize($_GET['search'] ?? '');
        $page = max(1, (int)($_GET['p'] ?? 1));

        if ($role !== '' && !in_array($role, ['admin', 'doctor', 'patient'], true)) {
            $role = '';
        }

        $total = $this->users->countAll($role, $search);
        $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
        $users = $this->users->getAllPaginated($page, $role, $search);

        $pageTitle = 'Users';
        require __DIR__ . '/../views/users/list.php';
    }

    public function create(): void
    {
        Auth::requireRole('admin');

        $specializations = (new SpecializationModel())->getAll();
        $pageTitle = 'Create User';
        require __DIR__ . '/../views/users/create.php';
    }

    public function store(): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token.');
            redirect(url('page=users&action=create'));
        }

        $name = sanitize($_POST['name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = (string)($_POST['password'] ?? '');
        $role = $_POST['role'] ?? 'patient';
        $phone = sanitize($_POST['phone'] ?? '');

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('danger', 'Please enter a valid name and email address.');
            redirect(url('page=users&action=create'));
        }

        if (strlen($password) < 8) {
            flash('danger', 'Temporary password must be at least 8 characters.');
            redirect(url('page=users&action=create'));
        }

        if (!in_array($role, ['admin', 'doctor', 'patient'], true)) {
            flash('danger', 'Invalid user role.');
            redirect(url('page=users&action=create'));
        }

        if ($this->users->findByEmail($email)) {
            flash('danger', 'Email already exists.');
            redirect(url('page=users&action=create'));
        }

        $avatar = $this->handleAvatarUpload('avatar');
        if ($this->uploadFailed) {
            redirect(url('page=users&action=create'));
        }

        $userId = $this->users->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => $role,
            'phone' => $phone,
            'avatar' => $avatar,
            'first_login' => $role === 'admin' ? 0 : 1,
        ]);

        if (!$userId) {
            flash('danger', 'Could not create user.');
            redirect(url('page=users&action=create'));
        }

        if ($role === 'doctor') {
            $days = $_POST['available_days'] ?? ['Sun', 'Mon', 'Tue', 'Wed', 'Thu'];
            $validDays = array_values(array_intersect(dayOptions(), (array)$days));
            if (!$validDays) {
                $validDays = ['Sun'];
            }

            (new DoctorModel())->create([
                'user_id' => $userId,
                'specialization_id' => (int)($_POST['specialization_id'] ?? 1),
                'bio' => sanitize($_POST['bio'] ?? ''),
                'consultation_fee' => (float)($_POST['consultation_fee'] ?? 0),
                'available_days' => implode(',', $validDays),
            ]);
        }

        flash('success', 'User created successfully.');
        redirect(url('page=users'));
    }

    public function edit(): void
    {
        Auth::requireRole('admin');

        $id = (int)($_GET['id'] ?? 0);
        $user = $this->users->findById($id);

        if (!$user) {
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $pageTitle = 'Edit User';
        require __DIR__ . '/../views/users/edit.php';
    }

    public function update(): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token.');
            redirect(url('page=users'));
        }

        $id = (int)($_POST['id'] ?? 0);
        $user = $this->users->findById($id);

        if (!$user) {
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $data = [
            'name' => sanitize($_POST['name'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($data['name'] === '') {
            flash('danger', 'Name is required.');
            redirect(url('page=users&action=edit&id=' . $id));
        }

        if ($id === Auth::id()) {
            $data['is_active'] = 1;
        }

        $avatar = $this->handleAvatarUpload('avatar', $user['avatar'] ?? null);
        if ($this->uploadFailed) {
            redirect(url('page=users&action=edit&id=' . $id));
        }
        if ($avatar !== null) {
            $data['avatar'] = $avatar;
        }

        $this->users->update($id, $data);

        if ($id === Auth::id()) {
            $_SESSION['user']['name'] = $data['name'];
        }

        flash('success', 'User updated successfully.');
        redirect(url('page=users'));
    }


    public function profile(): void
    {
        Auth::requireRole('patient');

        $user = $this->users->findById(Auth::id());
        if (!$user) {
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $pageTitle = 'My Profile';
        require __DIR__ . '/../views/users/profile.php';
    }

    public function update_profile(): void
    {
        Auth::requireRole('patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token.');
            redirect(url('page=users&action=profile'));
        }

        $user = $this->users->findById(Auth::id());
        if (!$user) {
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $data = [
            'name' => sanitize($_POST['name'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
        ];

        if ($data['name'] === '') {
            flash('danger', 'Name is required.');
            redirect(url('page=users&action=profile'));
        }

        $avatar = $this->handleAvatarUpload('avatar', $user['avatar'] ?? null);
        if ($this->uploadFailed) {
            redirect(url('page=users&action=profile'));
        }
        if ($avatar !== null) {
            $data['avatar'] = $avatar;
        }

        $this->users->update(Auth::id(), $data);
        $_SESSION['user']['name'] = $data['name'];

        flash('success', 'Profile updated successfully.');
        redirect(url('page=users&action=profile'));
    }

    public function toggle(): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token.');
            redirect(url('page=users'));
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id === Auth::id()) {
            flash('danger', 'You cannot deactivate your own account.');
            redirect(url('page=users'));
        }

        $target = $this->users->findById($id);
        if (!$target) {
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $this->users->toggleActive($id);
        flash('success', 'User account status changed.');
        redirect(url('page=users'));
    }

    public function password(): void
    {
        Auth::requireRole('admin');

        $id = (int)($_GET['id'] ?? 0);
        $user = $this->users->findById($id);

        if (!$user) {
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $pageTitle = 'Change User Password';
        require __DIR__ . '/../views/users/password.php';
    }

    public function update_password(): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token.');
            redirect(url('page=users'));
        }

        $id = (int)($_POST['id'] ?? 0);
        $new = (string)($_POST['new_password'] ?? '');

        if (!$this->users->findById($id)) {
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        if (strlen($new) < 8) {
            flash('danger', 'Password must be at least 8 characters.');
            redirect(url('page=users&action=password&id=' . $id));
        }

        $this->users->updatePassword($id, password_hash($new, PASSWORD_BCRYPT));
        flash('success', 'Password changed successfully.');
        redirect(url('page=users'));
    }

    private function handleAvatarUpload(string $field, ?string $oldPath = null): ?string
    {
        $this->uploadFailed = false;

        if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            $this->uploadFailed = true;
            flash('danger', uploadErrorMessage($_FILES[$field]['error']));
            return null;
        }

        if ($_FILES[$field]['size'] > MAX_IMAGE_SIZE) {
            $this->uploadFailed = true;
            flash('danger', 'Avatar image must not exceed 1MB.');
            return null;
        }

        $info = getimagesize($_FILES[$field]['tmp_name']);
        if (!$info || !in_array($info['mime'], ['image/jpeg', 'image/png'], true)) {
            $this->uploadFailed = true;
            flash('danger', 'Avatar must be a valid JPEG or PNG image.');
            return null;
        }

        $extension = $info['mime'] === 'image/png' ? 'png' : 'jpg';
        $filename = 'avatar_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;

        if (!is_dir(UPLOAD_AVATAR_PATH)) {
            mkdir(UPLOAD_AVATAR_PATH, 0775, true);
        }

        if (!move_uploaded_file($_FILES[$field]['tmp_name'], UPLOAD_AVATAR_PATH . $filename)) {
            $this->uploadFailed = true;
            flash('danger', 'Could not save avatar image.');
            return null;
        }

        $this->deleteOldAvatar($oldPath);

        return 'public/uploads/avatars/' . $filename;
    }

    private function deleteOldAvatar(?string $oldPath): void
    {
        if (!$oldPath || strpos($oldPath, 'public/uploads/avatars/') !== 0) {
            return;
        }

        $fullPath = ROOT_PATH . '/' . $oldPath;
        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
