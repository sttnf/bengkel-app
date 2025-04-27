<?php

namespace App\Core;

class Migration
{
    /**
     * PDO instance
     *
     * @var \PDO
     */
    public $connection;

    /**
     * Create a new table
     *
     * @param string $tableName
     * @param callable $callback
     */
    protected function createTable($tableName, callable $callback)
    {
        $blueprint = new Blueprint($tableName);
        $callback($blueprint);

        $sql = $blueprint->toSQL();
        $this->connection->exec($sql);
    }

    /**
     * Drop a table
     *
     * @param string $tableName
     */
    protected function dropTable($tableName)
    {
        $sql = "DROP TABLE IF EXISTS {$tableName}";
        $this->connection->exec($sql);
    }
}

/**
 * Blueprint class for database schema definitions
 */
class Blueprint
{
    protected $tableName;
    protected $columns = [];
    protected $primaryKey;
    protected $foreignKeys = [];

    /**
     * Create a new blueprint instance
     *
     * @param string $tableName
     */
    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Add an auto-incrementing ID column
     *
     * @return $this
     */
    public function id()
    {
        $this->columns['id'] = [
            'type' => 'BIGINT',
            'autoIncrement' => true,
            'notNull' => true
        ];
        $this->primaryKey = 'id';
        return $this;
    }

    /**
     * Add an integer column
     *
     * @param string $name
     * @param int|null $length
     * @return $this
     */
    public function integer($name, $length = null)
    {
        $this->columns[$name] = [
            'type' => 'INT',
            'length' => $length
        ];
        return $this;
    }

    /**
     * Add a big integer column
     *
     * @param string $name
     * @return $this
     */
    public function bigInteger($name)
    {
        $this->columns[$name] = [
            'type' => 'BIGINT'
        ];
        return $this;
    }

    /**
     * Add a string column
     *
     * @param string $name
     * @param int $length
     * @return $this
     */
    public function string($name, $length = 255)
    {
        $this->columns[$name] = [
            'type' => 'VARCHAR',
            'length' => $length
        ];
        return $this;
    }

    /**
     * Add a text column
     *
     * @param string $name
     * @return $this
     */
    public function text($name)
    {
        $this->columns[$name] = [
            'type' => 'TEXT'
        ];
        return $this;
    }

    /**
     * Add a boolean column
     *
     * @param string $name
     * @return $this
     */
    public function boolean($name)
    {
        $this->columns[$name] = [
            'type' => 'BOOLEAN'
        ];
        return $this;
    }

    /**
     * Add a datetime column
     *
     * @param string $name
     * @return $this
     */
    public function datetime($name)
    {
        $this->columns[$name] = [
            'type' => 'DATETIME'
        ];
        return $this;
    }

    /**
     * Add a timestamp column
     *
     * @param string $name
     * @return $this
     */
    public function timestamp($name)
    {
        $this->columns[$name] = [
            'type' => 'TIMESTAMP'
        ];
        return $this;
    }

    /**
     * Add a decimal column
     *
     * @param string $name
     * @param int $precision
     * @param int $scale
     * @return $this
     */
    public function decimal($name, $precision = 8, $scale = 2)
    {
        $this->columns[$name] = [
            'type' => 'DECIMAL',
            'precision' => $precision,
            'scale' => $scale
        ];
        return $this;
    }

    /**
     * Add an enum column
     *
     * @param string $name
     * @param array $values
     * @return $this
     */
    public function enum($name, array $values)
    {
        $valuesString = implode(', ', array_map(function($value) {
            return "'{$value}'";
        }, $values));

        $this->columns[$name] = [
            'type' => 'ENUM',
            'values' => $valuesString
        ];
        return $this;
    }

    /**
     * Add created_at and updated_at timestamp columns
     *
     * @return $this
     */
    public function timestamps()
    {
        $this->columns['created_at'] = [
            'type' => 'TIMESTAMP',
            'default' => 'CURRENT_TIMESTAMP'
        ];
        $this->columns['updated_at'] = [
            'type' => 'TIMESTAMP',
            'default' => 'CURRENT_TIMESTAMP',
            'onUpdate' => 'CURRENT_TIMESTAMP'
        ];
        return $this;
    }

    /**
     * Set column to NOT NULL
     *
     * @return $this
     */
    public function notNull()
    {
        $lastColumn = array_key_last($this->columns);
        $this->columns[$lastColumn]['notNull'] = true;
        return $this;
    }

    /**
     * Set column to be nullable
     *
     * @return $this
     */
    public function nullable()
    {
        $lastColumn = array_key_last($this->columns);
        $this->columns[$lastColumn]['notNull'] = false;
        return $this;
    }

    /**
     * Set column default value
     *
     * @param mixed $value
     * @return $this
     */
    public function default($value)
    {
        $lastColumn = array_key_last($this->columns);
        $this->columns[$lastColumn]['default'] = $value;
        return $this;
    }

    /**
     * Set column to be unique
     *
     * @return $this
     */
    public function unique()
    {
        $lastColumn = array_key_last($this->columns);
        $this->columns[$lastColumn]['unique'] = true;
        return $this;
    }

    /**
     * Set primary key
     *
     * @param string|array $columns
     * @return $this
     */
    public function primaryKey($columns)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->primaryKey = $columns;
        return $this;
    }

    /**
     * Add a foreign key constraint
     *
     * @param string $column
     * @return $this
     */
    public function foreignKey($column)
    {
        $this->foreignKeys[$column] = [];
        return $this;
    }

    /**
     * Specify the referenced column
     *
     * @param string $column
     * @return $this
     */
    public function references($column)
    {
        $lastFK = array_key_last($this->foreignKeys);
        $this->foreignKeys[$lastFK]['references'] = $column;
        return $this;
    }

    /**
     * Specify the referenced table
     *
     * @param string $table
     * @return $this
     */
    public function on($table)
    {
        $lastFK = array_key_last($this->foreignKeys);
        $this->foreignKeys[$lastFK]['on'] = $table;
        return $this;
    }

    /**
     * Specify the ON DELETE action
     *
     * @param string $action
     * @return $this
     */
    public function onDelete($action)
    {
        $lastFK = array_key_last($this->foreignKeys);
        $this->foreignKeys[$lastFK]['onDelete'] = $action;
        return $this;
    }

    /**
     * Specify the ON UPDATE action
     *
     * @param string $action
     * @return $this
     */
    public function onUpdate($action)
    {
        $lastFK = array_key_last($this->foreignKeys);
        $this->foreignKeys[$lastFK]['onUpdate'] = $action;
        return $this;
    }

    /**
     * Set column to auto increment
     *
     * @return $this
     */
    public function autoIncrement()
    {
        $lastColumn = array_key_last($this->columns);
        $this->columns[$lastColumn]['autoIncrement'] = true;
        return $this;
    }

    /**
     * Generate the SQL for creating the table
     *
     * @return string
     */
    public function toSQL()
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->tableName} (\n";

        $columnDefinitions = [];

        foreach ($this->columns as $name => $column) {
            $columnDefinitions[] = "    " . $this->getColumnDefinition($name);
        }

        // Add primary key
        if ($this->primaryKey) {
            if (is_array($this->primaryKey)) {
                $keys = implode(', ', $this->primaryKey);
                $columnDefinitions[] = "    PRIMARY KEY ({$keys})";
            } else {
                $columnDefinitions[] = "    PRIMARY KEY ({$this->primaryKey})";
            }
        }

        // Add foreign keys
        foreach ($this->foreignKeys as $column => $references) {
            if (isset($references['references']) && isset($references['on'])) {
                $fk = "    FOREIGN KEY ({$column}) REFERENCES {$references['on']}({$references['references']})";

                if (isset($references['onDelete'])) {
                    $fk .= " ON DELETE {$references['onDelete']}";
                }

                if (isset($references['onUpdate'])) {
                    $fk .= " ON UPDATE {$references['onUpdate']}";
                }

                $columnDefinitions[] = $fk;
            }
        }

        $sql .= implode(",\n", $columnDefinitions);
        $sql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        return $sql;
    }

    /**
     * Generate the SQL for a column definition
     *
     * @param string $name
     * @return string
     */
    public function getColumnDefinition($name)
    {
        $column = $this->columns[$name];
        $def = "{$name} {$column['type']}";

        if (isset($column['length']) && $column['length']) {
            $def .= "({$column['length']})";
        }

        if (isset($column['precision']) && isset($column['scale'])) {
            $def .= "({$column['precision']}, {$column['scale']})";
        }

        if (isset($column['values'])) {
            $def .= "({$column['values']})";
        }

        if (isset($column['notNull']) && $column['notNull']) {
            $def .= " NOT NULL";
        }

        if (isset($column['default'])) {
            if ($column['default'] === 'CURRENT_TIMESTAMP') {
                $def .= " DEFAULT CURRENT_TIMESTAMP";
            } else {
                $def .= " DEFAULT " . (is_string($column['default']) && $column['default'] !== 'NULL' ? "'{$column['default']}'" : $column['default']);
            }
        }

        if (isset($column['onUpdate']) && $column['onUpdate'] === 'CURRENT_TIMESTAMP') {
            $def .= " ON UPDATE CURRENT_TIMESTAMP";
        }

        if (isset($column['autoIncrement']) && $column['autoIncrement']) {
            $def .= " AUTO_INCREMENT";
        }

        if (isset($column['unique']) && $column['unique']) {
            $def .= " UNIQUE";
        }

        return $def;
    }
}