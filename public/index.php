<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../routes/web.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$router = new \App\Core\Router();

echo $router->resolve();