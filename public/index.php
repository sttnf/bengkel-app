<?php

use App\Controllers\AuthController;
use App\Controllers\UserController;

require_once __DIR__ . '/../vendor/autoload.php';

$router = new \App\Core\Router();

// Serve static files (MOVED TO THE TOP)
$router->get('/css/:filename', function ($filename) use ($router) {
    $router->serveStaticFile('css/' . $filename);
});

$router->get('/js/:filename', function ($filename) use ($router) {
    $router->serveStaticFile('js/' . $filename);
});

// Home route
$router->get('/', fn() => $router->renderView('home'));

// User service routes
$router->get('/service', fn() => $router->renderView('users/service'));
$router->post('/service', fn() => $router->renderView('users/service'));

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

echo $router->resolve();