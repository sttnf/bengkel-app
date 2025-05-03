<?php
// Check if the application is up
$status = 'healthy';
$message = 'OK';
$statusCode = 200;

// Basic database connection check (optional)
try {
    $host = getenv('DB_HOST') ?: 'mysql';
    $dbname = getenv('DB_DATABASE') ?: 'app_db';
    $username = getenv('DB_USERNAME') ?: 'app_user';
    $password = getenv('DB_PASSWORD') ?: 'password';

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Perform a simple query
    $stmt = $pdo->query('SELECT 1');
    $db_status = 'connected';
} catch (PDOException $e) {
    $status = 'degraded';
    $message = 'Database connection issue';
    $db_status = 'disconnected';
    $statusCode = 500;
}

// Return JSON response
header('Content-Type: application/json');
http_response_code($statusCode);

echo json_encode([
    'status' => $status,
    'message' => $message,
    'timestamp' => date('c'),
    'database' => $db_status ?? 'not_checked',
]);