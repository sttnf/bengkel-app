<?php
session_start();

use App\Controllers\{
    AuthController, DashboardController, HealthController, PaymentController, ServiceController, UserController
};
use App\Core\Router;

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
if (class_exists('Dotenv\Dotenv') && file_exists(__DIR__ . '/../.env')) {
    Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->load();
}

$router = new Router();

// Static file routes
$router->get('/css/:filename', fn($filename) => $router->serveStaticFile("css/$filename"));
$router->get('/js/:filename', fn($filename) => $router->serveStaticFile("js/$filename"));

// Health check route
$router->get('/health', [HealthController::class, 'index']);

// General routes
$router->get('/', fn() => $router->renderView('home'));

// Service routes
$router->get('/service', [ServiceController::class, 'index']);
$router->post('/service', [ServiceController::class, 'index']);
$router->get('/service/get-times', [ServiceController::class, 'getAvailableServiceTimes']);

// Authentication routes
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);

$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'register']);

$router->post('/logout', [AuthController::class, 'logout']);

// Dashboard routes
$router->group('/dashboard', function ($router) {
    $router->get('/', [DashboardController::class, 'index']);

    $router->get('/service-requests', [DashboardController::class, 'serviceRequests']);
    $router->post('/service-requests', [DashboardController::class, 'updateServiceRequest']);

    $router->get('/customers', [DashboardController::class, 'customers']);

    $router->get('/inventory', [DashboardController::class, 'inventory']);
    $router->post('/inventory', [DashboardController::class, 'inventory']);

    $router->get('/technicians', [DashboardController::class, 'technicians']);
    $router->get('/services', [DashboardController::class, 'services']);
    $router->post('/services', [DashboardController::class, 'services']);
    $router->get('/analytics', [DashboardController::class, 'analytics']);

    $router->group('/customer', function ($router) {
        $router->get('/', [DashboardController::class, 'customerIndex']);
        $router->get('/payment', [PaymentController::class, 'index']);
        $router->post('/payment', [PaymentController::class, 'process']);
        $router->get('/invoice', [PaymentController::class, 'invoice']);
    });
});

echo $router->resolve();