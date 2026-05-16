<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController
{
    private ?UserModel $users = null;

    private function users(): UserModel
    {
        if ($this->users === null) {
            $this->users = new UserModel();
        }

        return $this->users;
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
            return;
        }

        if (Auth::check()) {
            redirect(url('page=dashboard'));
        }

        $pageTitle = 'Login';
        require __DIR__ . '/../views/auth/login.php';
    }

    private function handleLogin(): void
    {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token. Please try again.');
            redirect(url('page=auth&action=login'));
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = (string)($_POST['password'] ?? '');
        $user = $this->users()->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            flash('danger', 'Invalid credentials.');
            redirect(url('page=auth&action=login'));
        }

        if ((int)$user['is_active'] !== 1) {
            flash('danger', 'Account suspended. Contact admin.');
            redirect(url('page=auth&action=login'));
        }

        Auth::login($user);

        if ((int)($user['first_login'] ?? 0) === 1) {
            redirect(url('page=auth&action=change_password'));
        }

        redirect(url('page=dashboard'));
    }

    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('page=auth&action=login'));
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid logout token.');
            redirect(url('page=dashboard'));
        }

        Auth::logout();
        redirect(url('page=auth&action=login'));
    }

    public function change_password(): void
    {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleChangePassword();
            return;
        }

        $pageTitle = 'Change Password';
        require __DIR__ . '/../views/auth/change_password.php';
    }

    private function handleChangePassword(): void
    {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token. Please try again.');
            redirect(url('page=auth&action=change_password'));
        }

        $current = (string)($_POST['current_password'] ?? '');
        $new = (string)($_POST['new_password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');
        $user = $this->users()->findById(Auth::id());

        if (!$user || !password_verify($current, $user['password'])) {
            flash('danger', 'Current password is incorrect.');
            redirect(url('page=auth&action=change_password'));
        }

        if (strlen($new) < 8 || !preg_match('/[a-z]/', $new) || !preg_match('/[A-Z]/', $new) || !preg_match('/[0-9]/', $new)) {
            flash('danger', 'New password must be at least 8 characters and include uppercase, lowercase, and number.');
            redirect(url('page=auth&action=change_password'));
        }

        if ($new !== $confirm) {
            flash('danger', 'Password confirmation does not match.');
            redirect(url('page=auth&action=change_password'));
        }

        $this->users()->updatePassword(Auth::id(), password_hash($new, PASSWORD_BCRYPT));
        Auth::clearFirstLogin();

        flash('success', 'Password changed successfully.');
        redirect(url('page=dashboard'));
    }
}
