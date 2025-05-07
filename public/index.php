<?php
session_start();

use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Models\ServiceRequest;
use App\Core\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$router = new Router();

// Static file routes
$router->get('/css/:filename', fn($filename) => $router->serveStaticFile("css/$filename"));
$router->get('/js/:filename', fn($filename) => $router->serveStaticFile("js/$filename"));

// Routes
$router->get('/', fn() => $router->renderView('home'));
$router->get('/service', fn() => $router->renderView('users/service'));
$router->post('/service', fn() => $router->renderView('users/service'));
$router->get('/service/get-times', function () {
    $serviceId = $_GET['service_id'] ?? null;
    $date = $_GET['date'] ?? null;
    $categoryId = $_GET['category_id'] ?? null;

    if ($serviceId) {
        $serviceRequestModel = new ServiceRequest();
        $availableTimes = $serviceRequestModel->getAvailableServiceTimes($serviceId, $date, $categoryId);
        header('Content-Type: application/json');
        echo json_encode($availableTimes);
    }
});

// Authentication routes
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// User management routes
$router->get('/users', [UserController::class, 'index']);
$router->get('/users/create', [UserController::class, 'create']);
$router->post('/users', [UserController::class, 'store']);
$router->get('/users/:id', [UserController::class, 'show']);
$router->get('/users/:id/edit', [UserController::class, 'edit']);
$router->post('/users/:id', [UserController::class, 'update']);
$router->post('/users/:id/delete', [UserController::class, 'delete']);

// Dashboard routes
$router->get('/dashboard', function () use ($router) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit();
    }

    $view = $_SESSION['user_role'] === 'admin' ? 'dashboard/admin/index' : 'dashboard/user';
    $data = $_SESSION['user_role'] === 'admin' ? [
        'serviceRequestStats' => [
            'pending' => 5, 'in_progress' => 3, 'completed' => 10, 'cancelled' => 2
        ],
        'recentRequests' => [
            ['customer' => 'John Doe', 'vehicle' => 'Toyota Camry (2021)', 'service' => 'Oil Change', 'date' => '2025-05-01', 'status' => 'pending'],
            ['customer' => 'Jane Smith', 'vehicle' => 'Honda Accord (2019)', 'service' => 'Tire Rotation', 'date' => '2025-05-02', 'status' => 'in_progress'],
            ['customer' => 'Emily Johnson', 'vehicle' => 'Ford Focus (2018)', 'service' => 'Brake Inspection', 'date' => '2025-05-03', 'status' => 'completed'],
        ],
        'lowStockItems' => [
            ['name' => 'Item 1', 'current_stock' => 5, 'reorder_level' => 10],
            ['name' => 'Item 2', 'current_stock' => 3, 'reorder_level' => 5],
            ['name' => 'Item 3', 'current_stock' => 8, 'reorder_level' => 15],
        ]
    ] : [
        'userProfile' => [
            'name' => $_SESSION['user_name'] ?? 'User',
            'email' => $_SESSION['user_email'] ?? 'email@example.com',
            'phone' => $_SESSION['user_phone'] ?? '+62 812 3456 7890',
            'vehicles' => 2,
            'last_login' => date('Y-m-d H:i', strtotime('-1 hour')),
        ],
        'currentServiceRequests' => [
            ['id' => 1, 'service' => 'Oil Change', 'status' => 'pending', 'date' => '2025-05-05', 'vehicle' => 'Toyota Corolla 2020'],
            ['id' => 2, 'service' => 'Brake Replacement', 'status' => 'in_progress', 'date' => '2025-05-04', 'vehicle' => 'Honda Civic 2019'],
        ],
        'serviceHistory' => [
            ['service' => 'Tire Rotation', 'date' => '2025-04-20', 'vehicle' => 'Toyota Corolla 2020'],
            ['service' => 'Full Inspection', 'date' => '2025-03-15', 'vehicle' => 'Honda Civic 2019'],
        ]
    ];

    return $router->renderView($view, $data, 'dashboard');
});

// Admin dashboard sub-routes
$adminRoutes = ['service-requests', 'customers', 'inventory', 'technicians', 'services', 'analytics'];
foreach ($adminRoutes as $route) {
    $router->get("/dashboard/$route", function () use ($router, $route) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        return $router->renderView("dashboard/admin/$route", [
            'serviceRequests' => [
                ['id' => 1, 'customers' => 'John Doe', 'vehicle' => 'Toyota Camry (2021)', 'service' => 'Oil Change', 'date' => '2025-05-01', 'status' => 'pending'],
                ['id' => 2, 'customers' => 'Jane Smith', 'vehicle' => 'Honda Accord (2019)', 'service' => 'Tire Rotation', 'date' => '2025-05-02', 'status' => 'in_progress'],
                ['id' => 3, 'customers' => 'Emily Johnson', 'vehicle' => 'Ford Focus (2018)', 'service' => 'Brake Inspection', 'date' => '2025-05-03', 'status' => 'completed'],
            ]
        ], 'dashboard');
    });
}

echo $router->resolve();