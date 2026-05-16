<?php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../config/config.php';

class DoctorModel extends BaseModel
{
    private string $selectJoin = 'SELECT d.*, u.name AS doctor_name, u.email, u.phone, u.avatar, s.name AS specialization_name
        FROM doctors d
        INNER JOIN users u ON d.user_id = u.id
        INNER JOIN specializations s ON d.specialization_id = s.id';

    public function findById(int $id): ?array
    {
        $result = $this->execute($this->selectJoin . ' WHERE d.id = ? LIMIT 1', 'i', [$id]);
        return $result && $result->num_rows ? $result->fetch_assoc() : null;
    }

    public function findByUserId(int $userId): ?array
    {
        $result = $this->execute($this->selectJoin . ' WHERE d.user_id = ? LIMIT 1', 'i', [$userId]);
        return $result && $result->num_rows ? $result->fetch_assoc() : null;
    }

    public function getAll(): array
    {
        $result = $this->execute($this->selectJoin . ' WHERE u.is_active = 1 ORDER BY u.name ASC');
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAllPaginated(int $page): array
    {
        $offset = (max(1, $page) - 1) * ITEMS_PER_PAGE;
        $result = $this->execute($this->selectJoin . ' ORDER BY u.name ASC LIMIT ? OFFSET ?', 'ii', [ITEMS_PER_PAGE, $offset]);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countAll(): int
    {
        $result = $this->execute('SELECT COUNT(*) AS total FROM doctors');
        $row = $result ? $result->fetch_assoc() : ['total' => 0];
        return (int)$row['total'];
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO doctors (user_id, specialization_id, bio, consultation_fee, available_days) VALUES (?, ?, ?, ?, ?)';
        $params = [
            (int)$data['user_id'],
            (int)$data['specialization_id'],
            $data['bio'] ?? null,
            (float)($data['consultation_fee'] ?? 0),
            $data['available_days'] ?? 'Sun,Mon,Tue,Wed,Thu',
        ];
        $ok = $this->execute($sql, 'iisds', $params);
        return $ok ? $this->db->lastInsertId() : 0;
    }

    public function update(int $doctorId, array $data): bool
    {
        $sql = 'UPDATE doctors SET specialization_id = ?, bio = ?, consultation_fee = ?, available_days = ? WHERE id = ?';
        $params = [
            (int)$data['specialization_id'],
            $data['bio'] ?? '',
            (float)$data['consultation_fee'],
            $data['available_days'],
            $doctorId,
        ];
        return (bool)$this->execute($sql, 'isdsi', $params);
    }

    public function getAvailableDays(int $doctorId): array
    {
        $result = $this->execute('SELECT available_days FROM doctors WHERE id = ? LIMIT 1', 'i', [$doctorId]);
        if (!$result || !$result->num_rows) return [];
        $row = $result->fetch_assoc();
        return array_filter(array_map('trim', explode(',', $row['available_days'])));
    }
}