<?php
namespace App\Core;

class Schema {
    private $table;
    private $columns = [];
    private $primaryKey = null;
    private $foreignKeys = [];

    public function __construct($table) {
        $this->table = $table;
    }

    public function id() {
        $this->columns[] = "`id` INT UNSIGNED AUTO_INCREMENT";
        $this->primaryKey = "id";
        return $this;
    }

    public function string($name, $length = 255, $nullable = false) {
        $this->columns[] = "`$name` VARCHAR($length)" . ($nullable ? '' : ' NOT NULL');
        return $this;
    }

    public function integer($name, $nullable = false) {
        $this->columns[] = "`$name` INT" . ($nullable ? '' : ' NOT NULL');
        return $this;
    }

    public function text($name, $nullable = false) {
        $this->columns[] = "`$name` TEXT" . ($nullable ? '' : ' NOT NULL');
        return $this;
    }

    public function boolean($name, $default = false) {
        $defaultValue = $default ? 'DEFAULT 1' : 'DEFAULT 0';
        $this->columns[] = "`$name` TINYINT(1) NOT NULL $defaultValue";
        return $this;
    }

    public function timestamp($name, $nullable = false) {
        $this->columns[] = "`$name` TIMESTAMP" . ($nullable ? ' NULL' : ' NOT NULL');
        return $this;
    }

    public function timestamps() {
        $this->columns[] = "`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    public function foreignKey($column, $referenceTable, $referenceColumn = 'id') {
        $this->foreignKeys[] = "FOREIGN KEY (`$column`) REFERENCES `$referenceTable`(`$referenceColumn`) ON DELETE CASCADE";
        return $this;
    }

    public function enum($name, $values, $nullable = false) {
        $valuesList = "'" . implode("', '", $values) . "'";
        $this->columns[] = "`$name` ENUM($valuesList)" . ($nullable ? ' NULL' : ' NOT NULL');
        return $this;
    }

    public function getSql() {
        $sql = "CREATE TABLE `{$this->table}` (";
        $sql .= implode(', ', $this->columns);

        if ($this->primaryKey) {
            $sql .= ", PRIMARY KEY (`{$this->primaryKey}`)";
        }

        if (!empty($this->foreignKeys)) {
            $sql .= ", " . implode(', ', $this->foreignKeys);
        }

        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        return $sql;
    }
}