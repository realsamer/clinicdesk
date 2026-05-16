<?php
require_once __DIR__ . '/helpers.php';

class Auth
{
    public static function login(array $user): void
    {
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'role' => $user['role'],
            'first_login' => (int)($user['first_login'] ?? 0),
        ];
    }

    public static function logout(): void
    {
        session_unset();
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function role(): string
    {
        return $_SESSION['user']['role'] ?? '';
    }

    public static function id(): int
    {
        return (int)($_SESSION['user']['id'] ?? 0);
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            redirect(url('page=auth&action=login'));
        }
    }

    public static function requireRole(string ...$roles): void
    {
        if (!self::check()) {
            redirect(url('page=auth&action=login'));
        }

        if (!in_array(self::role(), $roles, true)) {
            redirect(url('page=error&action=403'));
        }
    }

    public static function enforceFirstLogin(string $page, string $action): void
    {
        if (!self::check()) {
            return;
        }

        if ((int)($_SESSION['user']['first_login'] ?? 0) !== 1) {
            return;
        }

        $allowed = $page === 'auth' && in_array($action, ['change_password', 'logout'], true);

        if (!$allowed) {
            redirect(url('page=auth&action=change_password'));
        }
    }

    public static function clearFirstLogin(): void
    {
        if (isset($_SESSION['user'])) {
            $_SESSION['user']['first_login'] = 0;
        }
    }

    public static function updateSessionName(string $name): void
    {
        if (isset($_SESSION['user'])) {
            $_SESSION['user']['name'] = $name;
        }
    }
}
