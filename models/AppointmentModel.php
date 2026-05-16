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
            'SELECT id FROM appointments
             WHERE doctor_id = ? AND appt_date = ? AND appt_time = ? AND status <> ?
             LIMIT 1',
            'isss',
            [$doctorId, $date, $time, 'cancelled']
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
        return $this->countFiltered('patient', $patientId, $filters);
    }

    public function getByDoctor(int $doctorId, int $page, array $filters = []): array
    {
        $offset = (max(1, $page) - 1) * ITEMS_PER_PAGE;
        $conditions = ['a.doctor_id = ?'];
        $params = [$doctorId];
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

    public function getAll(int $page, array $filters = []): array
    {
        $offset = (max(1, $page) - 1) * ITEMS_PER_PAGE;
        $conditions = [];
        $params = [];
        $types = '';

        $this->applyCommonFilters($conditions, $params, $types, $filters);

        $where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
        $params[] = ITEMS_PER_PAGE;
        $params[] = $offset;
        $types .= 'ii';

        $sql = $this->selectJoin . $where
            . ' ORDER BY a.appt_date DESC, a.appt_time DESC LIMIT ? OFFSET ?';

        $result = $this->execute($sql, $types, $params);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countFiltered(string $scope, int $scopeId, array $filters = []): int
    {
        $conditions = [];
        $params = [];
        $types = '';

        if ($scope === 'patient') {
            $conditions[] = 'a.patient_id = ?';
            $params[] = $scopeId;
            $types .= 'i';
        }

        if ($scope === 'doctor') {
            $conditions[] = 'a.doctor_id = ?';
            $params[] = $scopeId;
            $types .= 'i';
        }

        $this->applyCommonFilters($conditions, $params, $types, $filters);

        $where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
        $sql = 'SELECT COUNT(*) AS total
                FROM appointments a
                INNER JOIN users p ON a.patient_id = p.id' . $where;

        $result = $this->execute($sql, $types, $params);
        $row = $result ? $result->fetch_assoc() : ['total' => 0];

        return (int)$row['total'];
    }

    public function todayByDoctor(int $doctorId): array
    {
        $sql = $this->selectJoin . ' WHERE a.doctor_id = ? AND a.appt_date = CURDATE()
                ORDER BY a.appt_time ASC';
        $result = $this->execute($sql, 'i', [$doctorId]);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function updateStatus(int $id, string $status, string $notes = ''): bool
    {
        return (bool)$this->execute(
            'UPDATE appointments SET status = ?, doctor_notes = ? WHERE id = ?',
            'ssi',
            [$status, $notes, $id]
        );
    }

    public function updateNotes(int $id, string $notes): bool
    {
        return (bool)$this->execute(
            'UPDATE appointments SET doctor_notes = ? WHERE id = ?',
            'si',
            [$notes, $id]
        );
    }

    public function cancelPendingByPatient(int $appointmentId, int $patientId): bool
    {
        return (bool)$this->execute(
            'UPDATE appointments SET status = ? WHERE id = ? AND patient_id = ? AND status = ?',
            'siis',
            ['cancelled', $appointmentId, $patientId, 'pending']
        );
    }



    public function countToday(): int
    {
        $result = $this->execute('SELECT COUNT(*) AS total FROM appointments WHERE appt_date = CURDATE()');
        $row = $result ? $result->fetch_assoc() : ['total' => 0];

        return (int)$row['total'];
    }

    public function countThisWeekByStatus(): array
    {
        $result = $this->execute(
            'SELECT status, COUNT(*) AS total
             FROM appointments
             WHERE WEEK(appt_date) = WEEK(NOW()) AND YEAR(appt_date) = YEAR(NOW())
             GROUP BY status'
        );
        $data = ['pending' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[$row['status']] = (int)$row['total'];
            }
        }

        return $data;
    }

    public function recent(int $limit = 5): array
    {
        $sql = $this->selectJoin . ' ORDER BY a.created_at DESC LIMIT ?';
        $result = $this->execute($sql, 'i', [$limit]);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function doctorMonthlyStats(int $doctorId): array
    {
        $result = $this->execute(
            'SELECT status, COUNT(*) AS total
             FROM appointments
             WHERE doctor_id = ?
               AND MONTH(appt_date) = MONTH(CURDATE())
               AND YEAR(appt_date) = YEAR(CURDATE())
             GROUP BY status',
            'i',
            [$doctorId]
        );
        $data = ['total' => 0, 'pending' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $count = (int)$row['total'];
                $data[$row['status']] = $count;
                $data['total'] += $count;
            }
        }

        return $data;
    }

    public function upcomingByDoctor(int $doctorId, int $limit = 5): array
    {
        $sql = $this->selectJoin . '
            WHERE a.doctor_id = ?
              AND a.status IN (?, ?)
              AND (a.appt_date > CURDATE() OR (a.appt_date = CURDATE() AND a.appt_time >= CURTIME()))
            ORDER BY a.appt_date ASC, a.appt_time ASC
            LIMIT ?';

        $result = $this->execute($sql, 'issi', [$doctorId, 'pending', 'confirmed', $limit]);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function activeByPatient(int $patientId): array
    {
        $sql = $this->selectJoin . '
            WHERE a.patient_id = ? AND a.status IN (?, ?)
            ORDER BY a.appt_date ASC, a.appt_time ASC';

        $result = $this->execute($sql, 'iss', [$patientId, 'pending', 'confirmed']);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countCompletedByPatient(int $patientId): int
    {
        $result = $this->execute(
            'SELECT COUNT(*) AS total FROM appointments WHERE patient_id = ? AND status = ?',
            'is',
            [$patientId, 'completed']
        );
        $row = $result ? $result->fetch_assoc() : ['total' => 0];

        return (int)$row['total'];
    }

    public function nextUpcomingByPatient(int $patientId): ?array
    {
        $sql = $this->selectJoin . '
            WHERE a.patient_id = ?
              AND a.status IN (?, ?)
              AND (a.appt_date > CURDATE() OR (a.appt_date = CURDATE() AND a.appt_time >= CURTIME()))
            ORDER BY a.appt_date ASC, a.appt_time ASC
            LIMIT 1';

        $result = $this->execute($sql, 'iss', [$patientId, 'pending', 'confirmed']);

        return $result && $result->num_rows ? $result->fetch_assoc() : null;
    }


    public function getReport(array $filters): array
    {
        $conditions = ['a.appt_date BETWEEN ? AND ?'];
        $params = [$filters['start_date'], $filters['end_date']];
        $types = 'ss';

        if (!empty($filters['doctor_id'])) {
            $conditions[] = 'a.doctor_id = ?';
            $params[] = (int)$filters['doctor_id'];
            $types .= 'i';
        }

        if (!empty($filters['status']) && in_array($filters['status'], $this->validStatuses(), true)) {
            $conditions[] = 'a.status = ?';
            $params[] = $filters['status'];
            $types .= 's';
        }

        $sql = $this->selectJoin . ' WHERE ' . implode(' AND ', $conditions)
            . ' ORDER BY a.appt_date ASC, a.appt_time ASC';

        $result = $this->execute($sql, $types, $params);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
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

        if (!empty($filters['doctor_id'])) {
            $conditions[] = 'a.doctor_id = ?';
            $params[] = (int)$filters['doctor_id'];
            $types .= 'i';
        }

        if (!empty($filters['patient_name'])) {
            $conditions[] = 'p.name LIKE ?';
            $params[] = '%' . $filters['patient_name'] . '%';
            $types .= 's';
        }
    }

    private function validStatuses(): array
    {
        return ['pending', 'confirmed', 'completed', 'cancelled'];
    }
}
