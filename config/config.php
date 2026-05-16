<?php
// Main application settings for ClinicDesk.
// Keep BASE_URL updated if you rename the project folder.

date_default_timezone_set('Asia/Gaza');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// In the final project, errors should be logged instead of displayed to users.
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

const APP_NAME = 'ClinicDesk';
const BASE_URL = 'http://localhost/clinicdesk/';
const ITEMS_PER_PAGE = 10;

const MAX_IMAGE_SIZE = 1048576;   // 1 MB
const MAX_PDF_SIZE = 3145728;     // 3 MB

const ROOT_PATH = __DIR__ . '/..';
const PUBLIC_PATH = ROOT_PATH . '/public';
const UPLOAD_AVATAR_PATH = PUBLIC_PATH . '/uploads/avatars/';
const UPLOAD_DOCTOR_PHOTO_PATH = PUBLIC_PATH . '/uploads/doctor_photos/';
const UPLOAD_PRESCRIPTION_PATH = PUBLIC_PATH . '/uploads/prescriptions/';
