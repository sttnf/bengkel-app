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

    public function findOneBy(array $criteria, array $joins = [])
    {
        $query = "SELECT * FROM {$this->table}";

        // Add joins if provided
        if (!empty($joins)) {
            foreach ($joins as $join) {
                $type = $join['type'] ?? 'INNER';
                $table = $join['table'];
                $on = $join['on'];
                $query .= " {$type} JOIN {$table} ON {$on}";
            }
        }

        list($whereClause, $params) = $this->buildWhereClause($criteria);
        $query .= " WHERE {$whereClause}";

        return $this->db->fetch($query, $params);
    }

    public function findBy(array $criteria = [], array $orderBy = [], $limit = null, $offset = null)
    {
        $query = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($criteria)) {
            list($whereClause, $params) = $this->buildWhereClause($criteria);
            $query .= " WHERE {$whereClause}";
        }

        if (!empty($orderBy)) {
            $query .= " ORDER BY " . implode(', ', array_map(
                    fn($key, $value) => "$key $value",
                    array_keys($orderBy),
                    $orderBy
                ));
        }

        if ($limit !== null) {
            $query .= " LIMIT ?";
            $params[] = $limit;

            if ($offset !== null) {
                $query .= " OFFSET ?";
                $params[] = $offset;
            }
        }

        return $this->db->fetchAll($query, $params);
    }

    public function count(array $criteria = [])
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];

        if (!empty($criteria)) {
            list($whereClause, $params) = $this->buildWhereClause($criteria);
            $query .= " WHERE {$whereClause}";
        }

        $result = $this->db->fetch($query, $params);
        return $result ? (int)$result['count'] : 0;
    }

    public function exists(array $criteria)
    {
        return $this->count($criteria) > 0;
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

    public function updateWhere(array $criteria, array $data)
    {
        list($whereClause, $whereParams) = $this->buildWhereClause($criteria);
        $setClause = implode(' = ?, ', array_keys($data)) . ' = ?';

        $sql = "UPDATE {$this->table} SET $setClause WHERE {$whereClause}";
        $this->db->query($sql, [...array_values($data), ...$whereParams]);
    }

    public function delete($id)
    {
        $this->db->query("DELETE FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function deleteWhere(array $criteria)
    {
        list($whereClause, $params) = $this->buildWhereClause($criteria);
        $sql = "DELETE FROM {$this->table} WHERE {$whereClause}";
        $this->db->query($sql, $params);
    }

    protected function buildWhereClause(array $criteria)
    {
        $conditions = [];
        $params = [];

        foreach ($criteria as $key => $value) {
            if (is_array($value)) {
                if (count($value) === 0) {
                    continue;
                }

                // Handle IN operator
                if (isset($value['in'])) {
                    $placeholders = implode(', ', array_fill(0, count($value['in']), '?'));
                    $conditions[] = "$key IN ($placeholders)";
                    foreach ($value['in'] as $inValue) {
                        $params[] = $inValue;
                    }
                } // Handle comparison operators
                elseif (isset($value['operator']) && isset($value['value'])) {
                    $conditions[] = "$key {$value['operator']} ?";
                    $params[] = $value['value'];
                } // Handle BETWEEN operator
                elseif (isset($value['between'])) {
                    $conditions[] = "$key BETWEEN ? AND ?";
                    $params[] = $value['between'][0];
                    $params[] = $value['between'][1];
                } // Handle NULL checks
                elseif (isset($value['isNull']) && $value['isNull'] === true) {
                    $conditions[] = "$key IS NULL";
                } elseif (isset($value['isNotNull']) && $value['isNotNull'] === true) {
                    $conditions[] = "$key IS NOT NULL";
                } // Handle LIKE operator
                elseif (isset($value['like'])) {
                    $conditions[] = "$key LIKE ?";
                    $params[] = $value['like'];
                }
            } else {
                $conditions[] = "$key = ?";
                $params[] = $value;
            }
        }

        return [implode(' AND ', $conditions), $params];
    }
}