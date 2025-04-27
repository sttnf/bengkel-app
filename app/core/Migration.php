<?php
namespace App\Core;

abstract class Migration {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    abstract public function up();
    abstract public function down();

    public function createTable($table, $callback) {
        $schema = new Schema($table);
        $callback($schema);

        $this->db->exec($schema->getSql());
    }

    public function dropTable($table) {
        $this->db->exec("DROP TABLE IF EXISTS $table");
    }
}

