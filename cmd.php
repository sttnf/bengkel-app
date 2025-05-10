<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Symfony\Component\Process\Process;

// Use Symfony Process for server management


/**
 * PHP MVC Framework Server Manager
 *
 * Provides commands to manage the framework:
 * - Start the development web server (with optional migrations)
 * - Run database migrations
 * - Display help information
 * - Initialize framework
 * - Generate framework components
 */
const BASE_PATH = __DIR__;
const CURRENT_FILE = __FILE__;

// --- Helper Functions ---

/**
 * Displays colorful messages in the console.
 *
 * @param string $message The message to display.
 * @param string $type The message type (info, error, warning, title).
 *
 * @return void
 */
function output(string $message, string $type = 'info'): void
{
    $colors = [
        'info' => "\033[0;32m",    // Green
        'error' => "\033[0;31m",   // Red
        'warning' => "\033[0;33m", // Yellow
        'title' => "\033[1;34m",   // Blue
        'reset' => "\033[0m",       // Reset
    ];

    echo $colors[$type] . $message . $colors['reset'] . PHP_EOL;
}

/**
 * Parses command line options.
 *
 * @param array $args The command line arguments.
 *
 * @return array An associative array of options.
 */
function parseOptions(array $args): array
{
    $options = [];
    foreach ($args as $arg) {
        if (str_starts_with($arg, '--')) { // Use str_starts_with
            $option = substr($arg, 2);
            $parts = explode('=', $option, 2);
            $options[$parts[0]] = $parts[1] ?? true;
        }
    }

    return $options;
}

/**
 * Checks if the autoloader exists.
 *
 * @return void
 */
function checkAutoloader(): void
{
    if (!file_exists(BASE_PATH . '/vendor/autoload.php')) {
        output("Error: 'vendor/autoload.php' not found!", 'error');
        output("Please ensure you have run 'composer install'.", 'error');
        exit(1);
    }
}

/**
 * Loads environment variables from a .env file.
 *
 * @return void
 */
function loadEnvironment(): void
{
    try {
        $dotenv = Dotenv::createImmutable(BASE_PATH);
        $dotenv->load();
        $dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD']);
    } catch (InvalidPathException) {
        output("Warning: .env file not found. Using system environment variables or defaults.", 'warning');
    }
}

// --- Command Functions ---

/**
 * Runs database migrations.
 *
 * @return void
 * @throws Exception
 *
 */
function migrate(): void
{
    $db = Database::getInstance();
    $pdo = $db->connection;
    $migrationPath = BASE_PATH . '/database/Models';

    if (!is_dir($migrationPath)) {
        throw new Exception("Migration directory does not exist: {$migrationPath}");
    }

    $files = glob($migrationPath . '/*.sql');
    if (empty($files)) {
        output("No SQL files found in {$migrationPath}", 'warning');
        return; // Important: Exit gracefully if no migrations
    }

    try {
        foreach ($files as $file) {
            $sql = file_get_contents($file);
            if ($sql === false) {
                throw new Exception("Failed to read file: {$file}");
            }
            $pdo->exec($sql);
            output("✅ Executed migration from: " . basename($file), 'info');
        }
        output("🎉 Database migration completed successfully.", 'info');
    } catch (PDOException $e) {
        output("❌ Migration failed: " . $e->getMessage(), 'error');
    }
}

/**
 * Starts the development web server.
 *
 * @param array $options The command line options.
 *
 * @return void
 */
function startServer(array $options): void
{
    if (getenv('APP_ENV') && getenv('APP_ENV') !== 'development') {
        output("Warning: Avoid running migrations in production environment!", 'warning');
    }

    $host = $options['host'] ?? $_ENV['HOST'] ?? getenv('HOST') ?? 'localhost';
    $port = $options['port'] ?? $_ENV['PORT'] ?? getenv('PORT') ?? 8000;
    $docRoot = BASE_PATH . '/public';

    if (!is_dir($docRoot)) {
        output("Error: 'public' directory does not exist!", 'error');
        exit(1);
    }

    if (!isset($options['skip-migrate'])) {
        output("Auto-running migrations before starting server...", 'info');
        migrate();
    } else {
        output("Migration step skipped due to --skip-migrate option", 'warning');
    }

    output("Starting server at http://{$host}:{$port}", 'info');
    output("Document root: {$docRoot}", 'info');
    output("Press Ctrl+C to stop the server", 'warning');

    // Use Symfony Process for better control
    $process = new Process(['php', '-S', "{$host}:{$port}", '-t', $docRoot]);
    $process->setTimeout(null);
    $process->setIdleTimeout(null);
    $process->setTty(false);
    $process->run(function ($type, $buffer) {
        // Stream output to the console
        echo $buffer;
    });

    if (!$process->isSuccessful()) {
        output("Server stopped with error.", 'error');
        exit(1);
    }
}

/**
 * Generates a framework component (controller, model, migration).
 *
 * @param array $options The command line options.
 *
 * @return void
 */
function makeComponent(array $options): void
{
    $name = $options['name'] ?? '';
    $type = $options['type'] ?? '';

    if (empty($name) || empty($type)) {
        output("Error: Both --name and --type options are required for the make command.", 'error');
        exit(1);
    }

    $name = ucfirst($name);
    $type = strtolower($type);
    $path = '';
    $template = '';

    switch ($type) {
        case 'controller':
            $path = BASE_PATH . '/app/Controllers/' . $name . '.php';
            $template = <<<'EOT'
<?php

namespace App\Controllers;

use App\Core\Controller;

class %className% extends Controller
{
    /**
     * Handle the index action
     *
     * @return void
     */
    public function index(): void
    {
        $this->view('index');
    }
}
EOT;
            break;
        case 'model':
            $path = BASE_PATH . '/app/Models/' . $name . '.php';
            $template = <<<'EOT'
<?php

namespace App\Models;

use App\Core\Model;

class %className% extends Model
{
    protected string $table = '%table_name%';
    protected string $primaryKey = 'id';

    // Define other model properties and methods as needed
}
EOT;
            break;
        case 'migration':
            $dir = BASE_PATH . '/database/Models';
            $files = glob($dir . '/*.sql');
            $nextOrder = count($files) + 1;
            $prefix = sprintf('%02d', $nextOrder);
            $tableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));

            $operation = str_starts_with(strtolower($name), 'create') ? 'Create' :
                (str_starts_with(strtolower($name), 'alter') ? 'Alter' : 'Modify');
            $action = $operation === 'Create' ? 'CREATE TABLE IF NOT EXISTS' :
                ($operation === 'Alter' ? 'ALTER TABLE' : '/* Define your SQL operation here for');

            $fileName = $prefix . '_' . strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name)) . '.sql';
            $path = $dir . '/' . $fileName;
            $template = <<<EOT
/* {$operation} {$tableName} table */
{$action} {$tableName} (
    id INT AUTO_INCREMENT PRIMARY KEY,
    /* Add your columns here */
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOT;
            break;
        default:
            output("Error: Invalid component type. Use 'controller', 'model', or 'migration'.", 'error');
            exit(1);
    }

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

/**
 * Displays help information.
 *
 * @return void
 */
function showHelp(): void
{
    output("Kita Framework", 'title');
    echo PHP_EOL;
    output("Usage:", 'warning');
    echo "  php " . basename(CURRENT_FILE) . " [command] [options]" . PHP_EOL;
    echo PHP_EOL;
    output("Available commands:", 'warning');
    echo "  serve    Start the development web server (auto-runs migrations)" . PHP_EOL;
    echo "  migrate  Run database migrations manually" . PHP_EOL;
    echo "  help     Display this help message" . PHP_EOL;
    echo "  init     Initialize the framework (create directories and files)" . PHP_EOL;
    echo "  make     Generate framework components" . PHP_EOL;
    echo PHP_EOL;
    output("Options:", 'warning');
    echo "  --host=hostname  Set the hostname (default: localhost)" . PHP_EOL;
    echo "  --port=port      Set the port (default: 8000)" . PHP_EOL;
    echo "  --skip-migrate  Skip auto-migrations when starting server" . PHP_EOL;
    echo "  --name=name      Name of the component to generate (e.g., UserController)" . PHP_EOL;
    echo "  --type=type      Type of component to generate (controller, model, migration)" . PHP_EOL;
    echo PHP_EOL;
    output("Examples:", 'warning');
    echo "  php " . basename(CURRENT_FILE) . " serve" . PHP_EOL;
    echo "  php " . basename(CURRENT_FILE) . " serve --host=0.0.0.0 --port=3000 --skip-migrate" . PHP_EOL;
    echo "  php " . basename(CURRENT_FILE) . " migrate" . PHP_EOL;
    echo "  php " . basename(CURRENT_FILE) . " init" . PHP_EOL;
    echo "  php " . basename(CURRENT_FILE) . " make --name=UserController --type=controller" . PHP_EOL;
}

// --- Main Script ---

$command = $argv[1] ?? 'help';
$args = array_slice($argv, 2);
$options = parseOptions($args);

checkAutoloader();
loadEnvironment(); // Load .env variables

switch ($command) {
    case 'serve':
        startServer($options);
        break;
    case 'migrate':
        migrate();
        break;
    case 'init':
        break;
    case 'make':
        makeComponent($options);
        break;
    case 'help':
    default:
        showHelp();
        break;
}
