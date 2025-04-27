<?php
namespace App\Core;

abstract class Model {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findAll() {
        return $this->db->fetchAll("SELECT * FROM {$this->table}");
    }

    public function findById($id) {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function create($data) {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');

        $fieldsStr = implode(', ', $fields);
        $placeholdersStr = implode(', ', $placeholders);

        $sql = "INSERT INTO {$this->table} ($fieldsStr) VALUES ($placeholdersStr)";

        $this->db->query($sql, array_values($data));
        return $this->db->connection->lastInsertId();
    }

    public function update($id, $data) {
        $fields = array_keys($data);
        $setClause = implode(' = ?, ', $fields) . ' = ?';

        $sql = "UPDATE {$this->table} SET $setClause WHERE id = ?";

        $values = array_values($data);
        $values[] = $id;

        return $this->db->query($sql, $values);
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
}