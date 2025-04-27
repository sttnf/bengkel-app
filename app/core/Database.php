<?php
namespace App\Core;

class Database {
    private static ?Database $instance = null;
    private \PDO $connection;

    private function __construct() {
        $config = require_once __DIR__ . '/../../config/database.php';
        
        $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}";
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $this->connection = new \PDO($dsn, $config['user'], $config['pass'], $options);
        } catch (\PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance(): ?Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection(): \PDO
    {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
}