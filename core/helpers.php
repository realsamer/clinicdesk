<?php
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function url(string $query = ''): string
{
    return 'index.php' . ($query ? '?' . ltrim($query, '?') : '');
}

function asset(string $path): string
{
    return rtrim(BASE_URL, '/') . '/public/assets/' . ltrim($path, '/');
}

function uploadUrl(string $path): string
{
    return rtrim(BASE_URL, '/') . '/public/uploads/' . ltrim($path, '/');
}

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function sanitize($value): string
{
    return trim(filter_var((string)$value, FILTER_SANITIZE_SPECIAL_CHARS));
}

function formatDate(?string $date): string
{
    if (!$date) {
        return '';
    }

    return date('d M Y', strtotime($date));
}

function formatTime(?string $time): string
{
    if (!$time) {
        return '';
    }

    return date('H:i', strtotime($time));
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function old(string $key, string $default = ''): string
{
    return e($_POST[$key] ?? $_GET[$key] ?? $default);
}

function statusBadge(string $status): string
{
    $map = [
        'pending' => 'warning',
        'confirmed' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger',
    ];

    $class = $map[$status] ?? 'secondary';

    return '<span class="badge badge-' . $class . '">' . e(ucfirst($status)) . '</span>';
}

function dayOptions(): array
{
    return ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
}

function fixedTimeSlots(): array
{
    $slots = [];
    $start = strtotime('09:00');
    $end = strtotime('16:00');

    for ($time = $start; $time <= $end; $time += 30 * 60) {
        $slots[] = date('H:i', $time);
    }

    return $slots;
}

function uploadErrorMessage(int $code): string
{
    $messages = [
        UPLOAD_ERR_INI_SIZE => 'Uploaded file is too large.',
        UPLOAD_ERR_FORM_SIZE => 'Uploaded file is too large.',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION => 'Upload stopped by a PHP extension.',
    ];

    return $messages[$code] ?? 'Unknown upload error.';
}
