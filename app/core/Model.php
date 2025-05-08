<?php

namespace App\Core;

abstract class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findAll()
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table}");
    }

    public function findById($id)
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function create(array $data)
    {
        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)";
        $this->db->query($sql, array_values($data));

        return $this->db->connection->lastInsertId();
    }

    public function update($id, array $data)
    {
        $setClause = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE {$this->table} SET $setClause WHERE id = ?";

        $this->db->query($sql, [...array_values($data), $id]);
    }

    public function delete($id)
    {
        $this->db->query("DELETE FROM {$this->table} WHERE id = ?", [$id]);
    }
}