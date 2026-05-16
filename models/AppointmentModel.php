<?php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../config/config.php';

class AppointmentModel extends BaseModel
{
    private string $selectJoin = 'SELECT a.*, p.name AS patient_name, p.email AS patient_email,
        d.id AS doctor_record_id, du.name AS doctor_name, du.email AS doctor_email,
        s.name AS specialization_name, pr.id AS prescription_id
        FROM appointments a
        INNER JOIN users p ON a.patient_id = p.id
        INNER JOIN doctors d ON a.doctor_id = d.id
        INNER JOIN users du ON d.user_id = du.id
        INNER JOIN specializations s ON d.specialization_id = s.id
        LEFT JOIN prescriptions pr ON pr.appointment_id = a.id';

    public function book(array $data): bool
    {
        $sql = 'INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, status, reason)
                VALUES (?, ?, ?, ?, ?, ?)';

        return (bool)$this->execute($sql, 'iissss', [
            (int)$data['patient_id'],
            (int)$data['doctor_id'],
            $data['appt_date'],
            $data['appt_time'],
            $data['status'] ?? 'pending',
            $data['reason'] ?? null,
        ]);
    }

    public function hasConflict(int $doctorId, string $date, string $time): bool
    {
        $result = $this->execute(
            'SELECT id FROM appointments WHERE doctor_id = ? AND appt_date = ? AND appt_time = ? LIMIT 1',
            'iss',
            [$doctorId, $date, $time]
        );

        return $result && $result->num_rows > 0;
    }

    public function findById(int $id): ?array
    {
        $result = $this->execute($this->selectJoin . ' WHERE a.id = ? LIMIT 1', 'i', [$id]);

        return $result && $result->num_rows ? $result->fetch_assoc() : null;
    }

    public function getByPatient(int $patientId, int $page, array $filters = []): array
    {
        $offset = (max(1, $page) - 1) * ITEMS_PER_PAGE;
        $conditions = ['a.patient_id = ?'];
        $params = [$patientId];
        $types = 'i';

        $this->applyCommonFilters($conditions, $params, $types, $filters);

        $params[] = ITEMS_PER_PAGE;
        $params[] = $offset;
        $types .= 'ii';

        $sql = $this->selectJoin . ' WHERE ' . implode(' AND ', $conditions)
            . ' ORDER BY a.appt_date DESC, a.appt_time DESC LIMIT ? OFFSET ?';

        $result = $this->execute($sql, $types, $params);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countByPatient(int $patientId, array $filters = []): int
    {
        $conditions = ['a.patient_id = ?'];
        $params = [$patientId];
        $types = 'i';

        $this->applyCommonFilters($conditions, $params, $types, $filters);

        $sql = 'SELECT COUNT(*) AS total FROM appointments a WHERE ' . implode(' AND ', $conditions);
        $result = $this->execute($sql, $types, $params);
        $row = $result ? $result->fetch_assoc() : ['total' => 0];

        return (int)$row['total'];
    }

    public function cancelPendingByPatient(int $appointmentId, int $patientId): bool
    {
        return (bool)$this->execute(
            'UPDATE appointments SET status = ? WHERE id = ? AND patient_id = ? AND status = ?',
            'siis',
            ['cancelled', $appointmentId, $patientId, 'pending']
        );
    }

    private function applyCommonFilters(array &$conditions, array &$params, string &$types, array $filters): void
    {
        if (!empty($filters['status']) && in_array($filters['status'], $this->validStatuses(), true)) {
            $conditions[] = 'a.status = ?';
            $params[] = $filters['status'];
            $types .= 's';
        }

        if (!empty($filters['start_date'])) {
            $conditions[] = 'a.appt_date >= ?';
            $params[] = $filters['start_date'];
            $types .= 's';
        }

        if (!empty($filters['end_date'])) {
            $conditions[] = 'a.appt_date <= ?';
            $params[] = $filters['end_date'];
            $types .= 's';
        }
    }

    private function validStatuses(): array
    {
        return ['pending', 'confirmed', 'completed', 'cancelled'];
    }
}
