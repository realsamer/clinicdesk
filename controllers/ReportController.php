<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

class ReportController
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
        Auth::requireRole('admin');

        $doctors = $this->doctors->getAll();
        $filters = $this->readFilters();
        $errors = [];
        $reportRows = [];
        $statusSummary = $this->emptyStatusSummary();
        $totalShown = 0;
        $hasSubmitted = $this->hasSubmitted();

        if ($hasSubmitted) {
            $errors = $this->validateFilters($filters);

            if (empty($errors)) {
                $reportRows = $this->appointments->getReport($filters);
                $statusSummary = $this->buildStatusSummary($reportRows);
                $totalShown = count($reportRows);

                if (($_GET['export'] ?? '') === 'csv') {
                    $this->exportCsv($reportRows);
                }
            }
        }

        $pageTitle = 'Appointment Reports';
        require __DIR__ . '/../views/reports/index.php';
    }

    private function readFilters(): array
    {
        $status = sanitize($_GET['status'] ?? '');
        $startDate = sanitize($_GET['start_date'] ?? '');
        $endDate = sanitize($_GET['end_date'] ?? '');
        $doctorId = (int)($_GET['doctor_id'] ?? 0);

        if (!in_array($status, ['pending', 'confirmed', 'completed', 'cancelled'], true)) {
            $status = '';
        }

        if ($doctorId < 1) {
            $doctorId = 0;
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'doctor_id' => $doctorId,
            'status' => $status,
        ];
    }

    private function hasSubmitted(): bool
    {
        return isset($_GET['start_date'])
            || isset($_GET['end_date'])
            || isset($_GET['doctor_id'])
            || isset($_GET['status'])
            || isset($_GET['export']);
    }

    private function validateFilters(array $filters): array
    {
        $errors = [];

        if ($filters['start_date'] === '') {
            $errors[] = 'Start date is required.';
        } elseif (!$this->isValidDate($filters['start_date'])) {
            $errors[] = 'Start date is invalid.';
        }

        if ($filters['end_date'] === '') {
            $errors[] = 'End date is required.';
        } elseif (!$this->isValidDate($filters['end_date'])) {
            $errors[] = 'End date is invalid.';
        }

        if ($this->isValidDate($filters['start_date'])
            && $this->isValidDate($filters['end_date'])
            && $filters['start_date'] > $filters['end_date']
        ) {
            $errors[] = 'Start date must be before or equal to end date.';
        }

        return $errors;
    }

    private function isValidDate(string $date): bool
    {
        if ($date === '') {
            return false;
        }

        $parsed = DateTime::createFromFormat('Y-m-d', $date);

        return $parsed && $parsed->format('Y-m-d') === $date;
    }

    private function emptyStatusSummary(): array
    {
        return [
            'pending' => 0,
            'confirmed' => 0,
            'completed' => 0,
            'cancelled' => 0,
        ];
    }

    private function buildStatusSummary(array $rows): array
    {
        $summary = $this->emptyStatusSummary();

        foreach ($rows as $row) {
            $status = $row['status'] ?? '';
            if (array_key_exists($status, $summary)) {
                $summary[$status]++;
            }
        }

        return $summary;
    }

    private function exportCsv(array $rows): void
    {
        $fileName = 'clinicdesk_appointments_report_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM helps Excel open Arabic/UTF-8 text correctly if needed.
        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'Patient Name',
            'Doctor Name',
            'Specialization',
            'Date',
            'Time',
            'Status',
            'Reason',
        ]);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['patient_name'] ?? '',
                $row['doctor_name'] ?? '',
                $row['specialization_name'] ?? '',
                $row['appt_date'] ?? '',
                $row['appt_time'] ?? '',
                $row['status'] ?? '',
                $row['reason'] ?? '',
            ]);
        }

        fclose($output);
        exit;
    }
}
