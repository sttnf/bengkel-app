<?php

namespace App\Core;

class Database
{
    private static ?Database $instance = null;
    public \PDO $connection;

    private function __construct()
    {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $name = $_ENV['DB_NAME'] ?? '';
        $user = $_ENV['DB_USER'] ?? '';
        $pass = $_ENV['DB_PASSWORD'] ?? '';
        $port = $_ENV['DB_PORT'] ?? '3306';

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_TIMEOUT => 5,
            \PDO::ATTR_PERSISTENT => false, // Non-persistent connections may help
        ];

        try {
            // Correct parameter order: dsn, username, password, options
            $this->connection = new \PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            // Provide a detailed error message with troubleshooting steps
            $message = "Database connection failed: " . $e->getMessage() .
                "\n\nTroubleshooting steps:" .
                "\n1. Check if your database server is running at {$host}:{$port}" .
                "\n2. Verify your database credentials are correct" .
                "\n3. Ensure no firewall is blocking the connection" .
                "\n4. Confirm the database '{$name}' exists";

            throw new \PDOException($message, (int)$e->getCode(), $e);
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
}