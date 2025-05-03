<?php

use App\Core\Database;

require_once __DIR__ . '/vendor/autoload.php'; // composer autoload

/**
 * PHP MVC Framework Server Manager
 *
 * This script provides commands to:
 * 1. Start the development web server (with automatic migrations)
 * 2. Run database migrations
 * 3. Display help information
 * 4. Initialize framework
 * 5. Generate framework components
 */

const BASE_PATH = __DIR__;
const CURRENT_FILE = __FILE__;

// Parse command line arguments
$command = $argv[1] ?? 'help';
$args = array_slice($argv, 2);

// Database migration function
function migrate(): void
{
    $db = Database::getInstance();
    $pdo = $db->connection;
    $folderPath = __DIR__ . '/database/models';

    extracted($folderPath, $pdo);
}

/**
 * @param string $folderPath
 * @param PDO $pdo
 * @return void
 * @throws Exception
 */
function extracted(string $folderPath, PDO $pdo): void
{
    if (!is_dir($folderPath)) {
        throw new Exception("Directory does not exist: {$folderPath}");
    }

    // Check if the folder is empty
    if (count(glob($folderPath . '/*')) === 0) {
        throw new Exception("No SQL files found in {$folderPath}");
    }

    try {
        // Scan folder for SQL files
        $files = glob($folderPath . '/*.sql');
        if (!$files) {
            throw new Exception("No SQL files found in {$folderPath}");
        }

        foreach ($files as $file) {
            $sql = file_get_contents($file);

            if ($sql === false) {
                throw new Exception("Failed to read file: {$file}");
            }

            $pdo->exec($sql);
            echo "✅ Executed migration from: " . basename($file) . PHP_EOL;
        }

        echo "🎉 Database migration completed successfully.";
    } catch (Exception $e) {
        echo "❌ Migration failed: " . $e->getMessage() . PHP_EOL;
    }
}

// Display colorful messages
function output($message, $type = 'info')
{
    $colors = [
        'info' => "\033[0;32m",    // Green
        'error' => "\033[0;31m",   // Red
        'warning' => "\033[0;33m", // Yellow
        'title' => "\033[1;34m",   // Blue
        'reset' => "\033[0m"       // Reset
    ];

    echo $colors[$type] . $message . $colors['reset'] . PHP_EOL;
}

// Show help information
function showHelp()
{
    output("Kita Framework", 'title');
    echo PHP_EOL;
    output("Usage:", 'warning');
    echo "  php " . basename(CURRENT_FILE) . " [command] [options]" . PHP_EOL;
    echo PHP_EOL;
    output("Available commands:", 'warning');
    echo "  serve    Start the development web server (auto-runs migrations)" . PHP_EOL;
    echo "  migrate.php  Run database migrations manually" . PHP_EOL;
    echo "  help     Display this help message" . PHP_EOL;
    echo "  init     Initialize the framework (create directories and files)" . PHP_EOL;
    echo "  make     Generate framework components" . PHP_EOL;
    echo PHP_EOL;
    output("Options:", 'warning');
    echo "  --host=hostname  Set the hostname (default: localhost)" . PHP_EOL;
    echo "  --port=port      Set the port (default: 8000)" . PHP_EOL;
    echo "  --skip-migrate.php   Skip auto-migrations when starting server" . PHP_EOL;
    echo "  --name=name      Name of the component to generate (e.g., controller, model)" . PHP_EOL;
    echo "  --type=type      Type of component to generate (controller, model, migration)" . PHP_EOL;
    echo PHP_EOL;
    output("Examples:", 'warning');
    echo "  php " . basename(CURRENT_FILE) . " serve" . PHP_EOL;
    echo "  php " . basename(CURRENT_FILE) . " serve --host=0.0.0.0 --port=3000 --skip-migrate.php" . PHP_EOL;
    echo "  php " . basename(CURRENT_FILE) . " migrate.php" . PHP_EOL;
    echo "  php " . basename(CURRENT_FILE) . " init" . PHP_EOL;
    echo "  php " . basename(CURRENT_FILE) . " make --name=UserController --type=controller" . PHP_EOL;
}

// Parse command line options
function parseOptions($args)
{
    $options = [];

    foreach ($args as $arg) {
        if (strpos($arg, '--') === 0) {
            $option = substr($arg, 2);
            $parts = explode('=', $option);

            if (count($parts) === 2) {
                $options[$parts[0]] = $parts[1];
            } else {
                $options[$parts[0]] = true;
            }
        }
    }

    return $options;
}

// Check if autoload.php exists
function checkAutoloader()
{
    if (!file_exists(BASE_PATH . '/vendor/autoload.php')) {
        output("Warning: 'vendor/autoload.php' not found!", 'warning');
        output("The autoloader is required for running migrations and the application.", 'warning');
        output("Please ensure you have run composer install.", 'warning');
        echo PHP_EOL;

        // Ask if the user wants to continue
        echo "Continue anyway? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));

        if (strtolower($line) !== 'y') {
            output("Operation cancelled.", 'error');
            exit(1);
        }
    }
}


// Start development web server
function startServer($options): void
{
    $host = $options['host'] ?? getenv('HOST') ?? 'localhost';
    $port = $options['port'] ?? getenv('PORT') ?? 8000;

    // Check if the public directory exists
    if (!is_dir(BASE_PATH . '/public')) {
        output("Error: 'public' directory does not exist!", 'error');
        exit(1);
    }

    // Run migrations if not explicitly skipped
    if (!isset($options['skip-migrate.php'])) {
        output("Auto-running migrations before starting server...", 'info');

        migrate();
    } else {
        output("Migration step skipped due to --skip-migrate.php option", 'warning');
    }

    output("Starting server at http://{$host}:{$port}", 'info');
    output("Document root: " . BASE_PATH . "/public", 'info');
    output("Press Ctrl+C to stop the server", 'warning');

    // Use the built-in PHP server, and route all requests to the index.php in the public folder
    chdir(BASE_PATH . '/public');
    passthru("php -S {$host}:{$port} index.php");
}

// Create all necessary directories if they don't exist
function checkAndCreateDirectories()
{
    $directories = [
        '/app',
        '/app/Controllers',
        '/app/Models',
        '/app/Core',
        '/app/views',
        '/app/views/layouts',
        '/app/views/users',
        '/config',
        '/database',
        '/database/migrations',
        '/database/Models',  // Add Models directory for SQL files
        '/public',
        '/public/css',
        '/public/js',
        '/routes',
        '/vendor',
        '/commands',
    ];

    $filesCreated = false;

    foreach ($directories as $dir) {
        $path = BASE_PATH . $dir;
        if (!is_dir($path)) {
            output("Creating directory: {$dir}", 'info');
            mkdir($path, 0755, true);
        }
    }

    // Check if essential files exist, if not create them with basic structure
    $essentialFiles = [];

    $essentialFiles['/routes/web.php'] = <<<'EOT'
<?php
// Define your routes here
$router->get("/", function() use ($router) {
    return $router->renderView("home");
});
EOT;

    $essentialFiles['/public/index.php'] = <<<'EOT'
<?php
require_once __DIR__ . "/../vendor/autoload.php";

$router = new \App\Core\Router();

require_once __DIR__ . "/../routes/web.php";

echo $router->resolve();
EOT;

    $essentialFiles['/public/.htaccess'] = <<<'EOT'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Redirect trailing slashes if not a folder
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOT;

    $essentialFiles['/app/views/home.php'] = <<<'EOT'
<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Welcome to PHP MVC Framework</h1>
    <p class="text-gray-600 mb-4">A clean, simple and lightweight MVC framework for PHP applications.</p>
    <div class="mt-6">
        <a href="/users" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            View Users
        </a>
    </div>
</div>
EOT;

    $essentialFiles['/app/views/layouts/main.php'] = <<<'EOT'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP MVC App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/" class="text-xl font-bold text-indigo-600">PHP MVC</a>
                    </div>
                    <div class="ml-6 flex items-center space-x-4">
                        <a href="/" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900">Home</a>
                        <a href="/users" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900">Users</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            {{content}}
        </div>
    </main>

    <footer class="bg-white shadow-inner mt-8">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                PHP MVC Framework &copy; <?= date("Y") ?>
            </p>
        </div>
    </footer>
</body>
</html>
EOT;

    $essentialFiles['/config/database.php'] = <<<'EOT'
<?php
return [
    "host" => "localhost",
    "name" => "my_database",
    "user" => "root",
    "pass" => "",
    "charset" => "utf8mb4",
];
EOT;

    $essentialFiles['/config/app.php'] = <<<'EOT'
<?php
return [
    "name" => "PHP MVC Framework",
    "debug" => true,
    "url" => "http://localhost",
    "timezone" => "UTC",
    "locale" => "en"
];
EOT;

    $essentialFiles['/app/views/404.php'] = <<<'EOT'
<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6 text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">404</h1>
        <p class="text-xl text-gray-600 mb-6">Page not found</p>
        <a href="/" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Go Home
        </a>
    </div>
</div>
EOT;

    // Add an example SQL migration file with correct comment syntax
    $essentialFiles['/database/Models/init.sql'] = <<<'EOT'
/* Create users table */
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOT;

    foreach ($essentialFiles as $file => $content) {
        $filePath = BASE_PATH . $file;
        if (!file_exists($filePath)) {
            output("Creating file: {$file}", 'info');
            file_put_contents($filePath, $content);
            $filesCreated = true;
        }
    }

    if ($filesCreated) {
        output("\nBasic framework structure has been set up.", 'info');
        output("You can now run the server with: php server.php serve", 'info');
    }
}

// Generate a component (controller, model, etc.)
function makeComponent($options)
{
    $name = $options['name'] ?? '';
    $type = $options['type'] ?? '';

    if (empty($name) || empty($type)) {
        output("Error: Both --name and --type options are required for the make command.", 'error');
        exit(1);
    }

    $name = ucfirst($name); // Ensure proper capitalization
    $type = strtolower($type); // Ensure lowercase for type comparison

    switch ($type) {
        case 'controller':
            $dir = '/app/Controllers';
            $template = <<<'EOT'
<?php

namespace App\Controllers;

class %className%
{
    public function index()
    {
        // TODO: Implement your controller logic here
        echo "This is the %className% controller";
    }
}
EOT;
            break;
        case 'model':
            $dir = '/app/Models';
            $template = <<<'EOT'
<?php

namespace App\Models;

use App\Core\Model;

class %className% extends Model
{
    // TODO: Define your model properties and methods here
    protected $table = '%table_name%'; // e.g., 'users'
    protected $primaryKey = 'id';
}
EOT;
            break;
        case 'migration':
            $dir = '/database/Models';
            // Use ordered prefix for migration file name
            $files = glob(BASE_PATH . $dir . '/*.sql');
            $nextOrder = count($files) + 1;
            $prefix = sprintf('%02d', $nextOrder);

            // Convert CamelCase to snake_case for table name
            $tableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
            if (strpos(strtolower($name), 'create') === 0) {
                $operation = "Create";
                $action = "CREATE TABLE IF NOT EXISTS";
            } else if (strpos(strtolower($name), 'alter') === 0) {
                $operation = "Alter";
                $action = "ALTER TABLE";
            } else {
                $operation = "Modify";
                $action = "/* Define your SQL operation here for";
            }

            $template = <<<EOT
/* {$operation} {$tableName} table */
{$action} {$tableName} (
    id INT AUTO_INCREMENT PRIMARY KEY,
    /* Add your columns here */
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOT;

            $name = $prefix . '_' . preg_replace('/(?<!^)[A-Z]/', '_$0', $name);
            $name = strtolower($name);
            $name = $name . '.sql';
            break;
        default:
            output("Error: Invalid component type. Use 'controller', 'model', or 'migration'.", 'error');
            exit(1);
    }

    $path = BASE_PATH . $dir . '/' . $name;
    if (file_exists($path)) {
        output("Error: File already exists: {$path}", 'error');
        exit(1);
    }

    $content = str_replace('%className%', $name, $template);
    $content = str_replace('%table_name%', strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace('Controller', '', $name))), $content);

    if (file_put_contents($path, $content) === false) {
        output("Error: Failed to create file: {$path}", 'error');
        exit(1);
    }

    output("Created file: {$path}", 'info');
}

// Execute command
switch ($command) {
    case 'serve':
        $options = parseOptions($args);
        checkAndCreateDirectories();
        checkAutoloader();
        startServer($options);
        break;
    case 'migrate.php':
        checkAndCreateDirectories();
        checkAutoloader();
        migrate();
        break;
    case 'init':
        checkAndCreateDirectories();
        output("Framework initialized successfully!", 'info');
        break;
    case 'make':
        $options = parseOptions($args);
        checkAndCreateDirectories();
        makeComponent($options);
        break;
    case 'help':
    default:
        showHelp();
        break;
}