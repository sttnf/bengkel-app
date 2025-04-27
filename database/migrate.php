<?php
require_once __DIR__ . '/../vendor/autoload.php';

$migrations = [
    new \Database\Migrations\CreateUsersTable()
];

$action = $argv[1] ?? 'up';

foreach ($migrations as $migration) {
    if ($action === 'up') {
        echo "Running migration: " . get_class($migration) . "\n";
        $migration->up();
    } else if ($action === 'down') {
        echo "Reverting migration: " . get_class($migration) . "\n";
        $migration->down();
    }
}

echo "Migration completed successfully.\n";