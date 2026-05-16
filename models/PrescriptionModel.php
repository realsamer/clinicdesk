<?php
require_once __DIR__ . '/BaseModel.php';

class PrescriptionModel extends BaseModel
{
    public function findByAppointmentId(int $appointmentId): ?array
    {
        $sql = 'SELECT pr.*, a.patient_id, a.doctor_id, a.status,
                    p.name AS patient_name, p.email AS patient_email,
                    du.name AS doctor_name, du.email AS doctor_email,
                    s.name AS specialization_name
                FROM prescriptions pr
                INNER JOIN appointments a ON pr.appointment_id = a.id
                INNER JOIN users p ON a.patient_id = p.id
                INNER JOIN doctors d ON a.doctor_id = d.id
                INNER JOIN users du ON d.user_id = du.id
                INNER JOIN specializations s ON d.specialization_id = s.id
                WHERE pr.appointment_id = ?
                LIMIT 1';

        $result = $this->execute($sql, 'i', [$appointmentId]);

        return $result && $result->num_rows ? $result->fetch_assoc() : null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO prescriptions (appointment_id, diagnosis, medications, notes, file_path)
                VALUES (?, ?, ?, ?, ?)';

        $ok = $this->execute($sql, 'issss', [
            (int)$data['appointment_id'],
            $data['diagnosis'],
            $data['medications'],
            $data['notes'] ?? null,
            $data['file_path'] ?? null,
        ]);

        return $ok ? $this->db->lastInsertId() : 0;
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE prescriptions
                SET diagnosis = ?, medications = ?, notes = ?, file_path = ?
                WHERE id = ?';

        return (bool)$this->execute($sql, 'ssssi', [
            $data['diagnosis'],
            $data['medications'],
            $data['notes'] ?? null,
            $data['file_path'] ?? null,
            $id,
        ]);
    }

    public function getByPatient(int $patientId): array
    {
        $sql = 'SELECT pr.*, a.appt_date, a.appt_time,
                    du.name AS doctor_name, s.name AS specialization_name
                FROM prescriptions pr
                INNER JOIN appointments a ON pr.appointment_id = a.id
                INNER JOIN doctors d ON a.doctor_id = d.id
                INNER JOIN users du ON d.user_id = du.id
                INNER JOIN specializations s ON d.specialization_id = s.id
                WHERE a.patient_id = ? AND a.status = ?
                ORDER BY pr.created_at DESC';

        $result = $this->execute($sql, 'is', [$patientId, 'completed']);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }


    public function countByPatient(int $patientId): int
    {
        $sql = 'SELECT COUNT(*) AS total
                FROM prescriptions pr
                INNER JOIN appointments a ON pr.appointment_id = a.id
                WHERE a.patient_id = ? AND a.status = ?';

        $result = $this->execute($sql, 'is', [$patientId, 'completed']);
        $row = $result ? $result->fetch_assoc() : ['total' => 0];

        return (int)$row['total'];
    }

}
