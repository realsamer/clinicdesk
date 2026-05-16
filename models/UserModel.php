<?php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../config/config.php';

class UserModel extends BaseModel
{
    public function findById(int $id): ?array
    {
        $result = $this->execute('SELECT * FROM users WHERE id = ? LIMIT 1', 'i', [$id]);

        return $result && $result->num_rows ? $result->fetch_assoc() : null;
    }

    public function findByEmail(string $email): ?array
    {
        $result = $this->execute('SELECT * FROM users WHERE email = ? LIMIT 1', 's', [$email]);

        return $result && $result->num_rows ? $result->fetch_assoc() : null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO users (name, email, password, role, phone, avatar, is_active, first_login) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $params = [
            $data['name'],
            $data['email'],
            $data['password'],
            $data['role'],
            $data['phone'] ?? null,
            $data['avatar'] ?? null,
            (int)($data['is_active'] ?? 1),
            (int)($data['first_login'] ?? 1),
        ];

        $ok = $this->execute($sql, 'ssssssii', $params);

        return $ok ? $this->db->lastInsertId() : 0;
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [];
        $types = '';

        foreach (['name' => 's', 'phone' => 's', 'avatar' => 's', 'is_active' => 'i'] as $field => $type) {
            if (array_key_exists($field, $data)) {
                $fields[] = $field . ' = ?';
                $params[] = $data[$field];
                $types .= $type;
            }
        }

        if (!$fields) {
            return true;
        }

        $params[] = $id;
        $types .= 'i';

        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';

        return (bool)$this->execute($sql, $types, $params);
    }

    public function updatePassword(int $id, string $newHash): bool
    {
        return (bool)$this->execute(
            'UPDATE users SET password = ?, first_login = 0 WHERE id = ?',
            'si',
            [$newHash, $id]
        );
    }

    public function getAllPaginated(int $page, string $role = '', string $search = ''): array
    {
        $offset = (max(1, $page) - 1) * ITEMS_PER_PAGE;
        $conditions = [];
        $params = [];
        $types = '';

        if ($role !== '') {
            $conditions[] = 'role = ?';
            $params[] = $role;
            $types .= 's';
        }

        if ($search !== '') {
            $conditions[] = '(name LIKE ? OR email LIKE ?)';
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $types .= 'ss';
        }

        $where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';

        $params[] = ITEMS_PER_PAGE;
        $params[] = $offset;
        $types .= 'ii';

        $result = $this->execute(
            "SELECT * FROM users $where ORDER BY created_at DESC LIMIT ? OFFSET ?",
            $types,
            $params
        );

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countAll(string $role = '', string $search = ''): int
    {
        $conditions = [];
        $params = [];
        $types = '';

        if ($role !== '') {
            $conditions[] = 'role = ?';
            $params[] = $role;
            $types .= 's';
        }

        if ($search !== '') {
            $conditions[] = '(name LIKE ? OR email LIKE ?)';
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $types .= 'ss';
        }

        $where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
        $result = $this->execute("SELECT COUNT(*) AS total FROM users $where", $types, $params);
        $row = $result ? $result->fetch_assoc() : ['total' => 0];

        return (int)$row['total'];
    }

    public function toggleActive(int $id): bool
    {
        return (bool)$this->execute(
            'UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?',
            'i',
            [$id]
        );
    }

    public function countByRole(): array
    {
        $result = $this->execute('SELECT role, COUNT(*) AS total FROM users GROUP BY role');
        $data = ['admin' => 0, 'doctor' => 0, 'patient' => 0];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[$row['role']] = (int)$row['total'];
            }
        }

        return $data;
    }
}
