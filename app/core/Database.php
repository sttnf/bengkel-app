<?php

namespace App\Core;
class Database
{
    private static ?Database $instance = null;
    public \PDO $connection;

    private function __construct()
    {
        $dsn = sprintf(
            "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
            $_ENV['DB_HOST'] ?? 'localhost',
            $_ENV['DB_PORT'] ?? '3306',
            $_ENV['DB_NAME'] ?? 'db_service_system'
        );

        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_TIMEOUT => 5,
            \PDO::ATTR_PERSISTENT => false,
        ];

        try {
            $this->connection = new \PDO(
                $dsn,
                $_ENV['DB_USER'] ?? 'root',
                $_ENV['DB_PASSWORD'] ?? '',
                $options
            );
        } catch (\PDOException $e) {
            throw new \PDOException(
                "Database connection failed: {$e->getMessage()}\n" .
                "1. Check if your database server is running.\n" .
                "2. Verify your credentials.\n" .
                "3. Ensure no firewall is blocking the connection.\n" .
                "4. Confirm the database exists.",
                (int)$e->getCode(),
                $e
            );
        }
    }

    public static function getInstance(): Database
    {
        return self::$instance ??= new self();
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function lastInsertId(?string $name = null): string
    {
        return $this->connection->lastInsertId($name);
    }
}