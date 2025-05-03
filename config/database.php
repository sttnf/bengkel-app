<?php
// config/database.php
return [
    'host' => getenv('DB_HOST'),
    'name' => getenv('DB_DATABASE'),
    'user' => getenv('DB_USERNAME'),
    'pass' => getenv('DB_PASSWORD'),
    'charset' => 'utf8mb4',
];