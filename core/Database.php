<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

class Database
{
    private static ?Database $instance = null;
    private mysqli $conn;

    private function __construct()
    {
        mysqli_report(MYSQLI_REPORT_OFF);

        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_errno) {
            error_log('Database connection failed: ' . $this->conn->connect_error);
            throw new RuntimeException('Database connection failed. Please try again later.');
        }

        $this->conn->set_charset('utf8mb4');
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function query(string $sql, string $types = '', array $params = [])
    {
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log('SQL prepare failed: ' . $this->conn->error . ' | SQL: ' . $sql);
            return false;
        }

        if ($types !== '' && !empty($params)) {
            if (!$stmt->bind_param($types, ...$params)) {
                error_log('SQL bind failed: ' . $stmt->error . ' | SQL: ' . $sql);
                return false;
            }
        }

        if (!$stmt->execute()) {
            error_log('SQL execute failed: ' . $stmt->error . ' | SQL: ' . $sql);
            return false;
        }

        $result = $stmt->get_result();

        if ($result instanceof mysqli_result) {
            return $result;
        }

        return true;
    }

    public function lastInsertId(): int
    {
        return $this->conn->insert_id;
    }
}
