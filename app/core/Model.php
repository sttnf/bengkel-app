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

    public function findAllPagination($limit = 10, $offset = 0, $criteria = [], array $joins = []): array
    {
        $query = $this->buildQueryWithJoins("SELECT * FROM {$this->table}", $joins);
        [$whereClause, $params] = $this->buildWhereClause($criteria);

        if ($whereClause) {
            $query .= " WHERE {$whereClause}";
        }
        $query .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        return $this->db->fetchAll($query, $params);
    }

    public function findById($id)
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function findOneBy(array $criteria, array $joins = [])
    {
        $query = $this->buildQueryWithJoins("SELECT * FROM {$this->table}", $joins);
        list($whereClause, $params) = $this->buildWhereClause($criteria);
        return $this->db->fetch("{$query} WHERE {$whereClause}", $params);
    }

    public function findBy(array $criteria = [], array $orderBy = [], $limit = null, $offset = null): array
    {
        $query = "SELECT * FROM {$this->table}";
        [$whereClause, $params] = $this->buildWhereClause($criteria);

        if ($whereClause) {
            $query .= " WHERE {$whereClause}";
        }
        if ($orderBy) {
            $query .= " ORDER BY " . $this->buildOrderByClause($orderBy);
        }
        if ($limit !== null) {
            $query .= " LIMIT ?";
            $params[] = $limit;
        }
        if ($offset !== null) {
            $query .= " OFFSET ?";
            $params[] = $offset;
        }

        return $this->db->fetchAll($query, $params);
    }

    public function count(array $criteria = [])
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        list($whereClause, $params) = $this->buildWhereClause($criteria);

        if ($whereClause) {
            $query .= " WHERE {$whereClause}";
        }

        return (int)($this->db->fetch($query, $params)['count'] ?? 0);
    }

    public function exists(array $criteria)
    {
        return $this->count($criteria) > 0;
    }

    public function create(array $data)
    {
        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $this->db->query("INSERT INTO {$this->table} ($fields) VALUES ($placeholders)", array_values($data));
        return $this->db->connection->lastInsertId();
    }

    public function update($id, array $data)
    {
        $setClause = $this->buildSetClause($data);
        $this->db->query("UPDATE {$this->table} SET {$setClause} WHERE id = ?", [...array_values($data), $id]);
    }

    public function updateWhere(array $criteria, array $data)
    {
        list($whereClause, $whereParams) = $this->buildWhereClause($criteria);
        $setClause = $this->buildSetClause($data);
        $this->db->query("UPDATE {$this->table} SET {$setClause} WHERE {$whereClause}", [...array_values($data), ...$whereParams]);
    }

    public function delete($id)
    {
        $this->db->query("DELETE FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function deleteWhere(array $criteria)
    {
        list($whereClause, $params) = $this->buildWhereClause($criteria);
        $this->db->query("DELETE FROM {$this->table} WHERE {$whereClause}", $params);
    }

    protected function buildWhereClause(array $criteria)
    {
        $conditions = [];
        $params = [];

        foreach ($criteria as $key => $value) {
            if (is_array($value)) {
                $this->processArrayCriteria($key, $value, $conditions, $params);
            } else {
                $conditions[] = "$key = ?";
                $params[] = $value;
            }
        }

        return [implode(' AND ', $conditions), $params];
    }

    private function processArrayCriteria($key, $value, &$conditions, &$params)
    {
        if (isset($value['in'])) {
            $placeholders = implode(', ', array_fill(0, count($value['in']), '?'));
            $conditions[] = "$key IN ($placeholders)";
            $params = array_merge($params, $value['in']);
        } elseif (isset($value['operator'], $value['value'])) {
            // Support comparing to another field
            if (is_string($value['value']) && !is_numeric($value['value']) && !str_contains($value['value'], "'")) {
                $conditions[] = "$key {$value['operator']} {$value['value']}";
            } else {
                $conditions[] = "$key {$value['operator']} ?";
                $params[] = $value['value'];
            }
        } elseif (isset($value['between'])) {
            $conditions[] = "$key BETWEEN ? AND ?";
            $params = array_merge($params, $value['between']);
        } elseif (!empty($value['isNull'])) {
            $conditions[] = "$key IS NULL";
        } elseif (!empty($value['isNotNull'])) {
            $conditions[] = "$key IS NOT NULL";
        } elseif (isset($value['like'])) {
            $conditions[] = "$key LIKE ?";
            $params[] = $value['like'];
        }
    }

    private function buildQueryWithJoins($baseQuery, array $joins)
    {
        foreach ($joins as $join) {
            $type = $join['type'] ?? 'INNER';
            $baseQuery .= " {$type} JOIN {$join['table']} ON {$join['on']}";
        }
        return $baseQuery;
    }

    private function buildOrderByClause(array $orderBy)
    {
        return implode(', ', array_map(fn($key, $value) => "$key $value", array_keys($orderBy), $orderBy));
    }

    private function buildSetClause(array $data)
    {
        return implode(' = ?, ', array_keys($data)) . ' = ?';
    }
}