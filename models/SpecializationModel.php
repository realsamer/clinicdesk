<?php
require_once __DIR__ . '/BaseModel.php';

class SpecializationModel extends BaseModel
{
    public function getAll(): array
    {
        $result = $this->execute('SELECT * FROM specializations ORDER BY name ASC');

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function findById(int $id): ?array
    {
        $result = $this->execute(
            'SELECT * FROM specializations WHERE id = ? LIMIT 1',
            'i',
            [$id]
        );

        return $result && $result->num_rows ? $result->fetch_assoc() : null;
    }

    public function create(string $name): bool
    {
        return (bool)$this->execute(
            'INSERT INTO specializations (name) VALUES (?)',
            's',
            [$name]
        );
    }

    public function update(int $id, string $name): bool
    {
        return (bool)$this->execute(
            'UPDATE specializations SET name = ? WHERE id = ?',
            'si',
            [$name, $id]
        );
    }

    public function isSafeToDelete(int $id): bool
    {
        $result = $this->execute(
            'SELECT COUNT(*) AS total FROM doctors WHERE specialization_id = ?',
            'i',
            [$id]
        );
        $row = $result ? $result->fetch_assoc() : ['total' => 1];

        return (int)$row['total'] === 0;
    }

    public function delete(int $id): bool
    {
        return (bool)$this->execute(
            'DELETE FROM specializations WHERE id = ?',
            'i',
            [$id]
        );
    }
}
