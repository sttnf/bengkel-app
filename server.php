<?php
/**
 * PHP MVC Framework Server Manager
 *
 * This script provides commands to:
 * 1. Start the development web server
 * 2. Run database migrations
 * 3. Display help information
 */

// Define the base path
const BASE_PATH = __DIR__;

// Parse command line arguments
$command = $argv[1] ?? 'help';
$args = array_slice($argv, 2);

// Display colorful messages
function output($message, $type = 'info') {
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
function showHelp() {
    output("PHP MVC Framework Server Manager", 'title');
    echo PHP_EOL;
    output("Usage:", 'warning');
    echo "  php server.php [command] [options]" . PHP_EOL;
    echo PHP_EOL;
    output("Available commands:", 'warning');
    echo "  serve    Start the development web server" . PHP_EOL;
    echo "  migrate  Run database migrations" . PHP_EOL;
    echo "  help     Display this help message" . PHP_EOL;
    echo PHP_EOL;
    output("Options:", 'warning');
    echo "  --host=hostname  Set the hostname (default: localhost)" . PHP_EOL;
    echo "  --port=port      Set the port (default: 8000)" . PHP_EOL;
    echo "  --direction=dir  Set migration direction (up/down, default: up)" . PHP_EOL;
    echo PHP_EOL;
    output("Examples:", 'warning');
    echo "  php server.php serve" . PHP_EOL;
    echo "  php server.php serve --host=0.0.0.0 --port=3000" . PHP_EOL;
    echo "  php server.php migrate" . PHP_EOL;
    echo "  php server.php migrate --direction=down" . PHP_EOL;
}

// Parse command line options
function parseOptions($args) {
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

// Start development web server
function startServer($options) {
    $host = $options['host'] ?? 'localhost';
    $port = $options['port'] ?? '8000';

    // Check if the public directory exists
    if (!is_dir(BASE_PATH . '/public')) {
        output("Error: 'public' directory does not exist!", 'error');
        exit(1);
    }

    output("Starting development server at http://{$host}:{$port}", 'info');
    output("Document root: " . BASE_PATH . "/public", 'info');
    output("Press Ctrl+C to stop the server", 'warning');

    // Change to the public directory and start the server
    chdir(BASE_PATH . '/public');
    passthru("php -S {$host}:{$port}");
}

// Check if autoload.php exists
function checkAutoloader() {
    if (!file_exists(BASE_PATH . '/vendor/autoload.php')) {
        output("Warning: 'vendor/autoload.php' not found!", 'warning');
        output("The autoloader is required for running migrations and the application.", 'warning');
        output("Please ensure you have created the autoloader file.", 'warning');
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

// Run migrations
function runMigrations($options) {
    checkAutoloader();

    // Check if migration script exists
    $migrationScript = BASE_PATH . '/database/migrate.php';
    if (!file_exists($migrationScript)) {
        output("Error: Migration script not found at {$migrationScript}", 'error');
        exit(1);
    }

    // Get migration direction
    $direction = $options['direction'] ?? 'up';
    if (!in_array($direction, ['up', 'down'])) {
        output("Error: Invalid migration direction. Use 'up' or 'down'.", 'error');
        exit(1);
    }

    output("Running migrations ({$direction})...", 'info');
    include $migrationScript;
}

// Create all necessary directories if they don't exist
function checkAndCreateDirectories() {
    $directories = [
        '/app',
        '/app/controllers',
        '/app/models',
        '/app/views',
        '/app/views/layouts',
        '/app/views/users',
        '/app/core',
        '/config',
        '/database',
        '/database/migrations',
        '/public',
        '/public/css',
        '/public/js',
        '/routes',
        '/vendor',
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
    $essentialFiles = [
        '/routes/web.php' => '<?php
// Define your routes here
$router->get("/", function() use ($router) {
    return $router->renderView("home");
});
',
        '/public/index.php' => '<?php
require_once __DIR__ . "/../vendor/autoload.php";

$router = new \\App\\Core\\Router();

require_once __DIR__ . "/../routes/web.php";

echo $router->resolve();
',
        '/public/.htaccess' => '<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Redirect trailing slashes if not a folder
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]
    
    # Handle front controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
',
        '/app/views/home.php' => '<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Welcome to PHP MVC Framework</h1>
    <p class="text-gray-600 mb-4">A clean, simple and lightweight MVC framework for PHP applications.</p>
    <div class="mt-6">
        <a href="/users" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            View Users
        </a>
    </div>
</div>
',
        '/app/views/layouts/main.php' => '<!DOCTYPE html>
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
',
        '/config/database.php' => '<?php
return [
    "host" => "localhost",
    "name" => "my_database",
    "user" => "root", 
    "pass" => "",
    "charset" => "utf8mb4",
];
',
        '/config/app.php' => '<?php
return [
    "name" => "PHP MVC Framework",
    "debug" => true,
    "url" => "http://localhost",
    "timezone" => "UTC",
    "locale" => "en"
];
',
        '/app/views/404.php' => '<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6 text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">404</h1>
        <p class="text-xl text-gray-600 mb-6">Page not found</p>
        <a href="/" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Go Home
        </a>
    </div>
</div>
'
    ];

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

// Execute command
switch ($command) {
    case 'serve':
        $options = parseOptions($args);
        checkAndCreateDirectories();
        startServer($options);
        break;

    case 'migrate':
        $options = parseOptions($args);
        checkAndCreateDirectories();
        runMigrations($options);
        break;

    case 'init':
        checkAndCreateDirectories();
        output("Framework initialized successfully!", 'info');
        break;

    case 'help':
    default:
        showHelp();
        break;
}